<?php

declare(strict_types=1);

require_once __DIR__.'/vendor/autoload.php';

use Dotenv\Dotenv;
use Fingerprint\ServerSdk\Api\FingerprintApi;
use Fingerprint\ServerSdk\ApiException;
use Fingerprint\ServerSdk\Configuration;
use Fingerprint\ServerSdk\Model\ErrorResponse;
use Fingerprint\ServerSdk\Model\Event;
use Fingerprint\ServerSdk\Model\EventUpdate;
use Fingerprint\ServerSdk\Webhook\WebhookVerifier;
use GuzzleHttp\Client;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

function env(string $key, ?string $default = null): ?string
{
    return $_ENV[$key] ?? getenv($key) ?: $default;
}

$apiKey = env('FP_PRIVATE_API_KEY');
if (!$apiKey) {
    throw new Exception('FP_PRIVATE_API_KEY is not defined.');
}

$regionEnv = env('FP_REGION', 'us');
$visitorIdToDelete = env('FP_VISITOR_ID_TO_DELETE');
$eventIdToUpdate = env('FP_EVENT_ID_TO_UPDATE');

$region = match(strtolower(trim($regionEnv))) {
    'eu' => Configuration::REGION_EUROPE,
    'ap' => Configuration::REGION_ASIA,
    default => Configuration::REGION_GLOBAL,
};

$config = new Configuration(
    $apiKey,
    $region,
);

$client = new FingerprintApi(
    $config,
    new Client()
);

$start = (new DateTime())->sub(new DateInterval('P29D'));
$end = new DateTime();

// FingerprintApi->searchEvents usage example
try {
    list($events, $response) = $client->searchEventsWithHttpInfo(2, start: $start->getTimestamp() * 1000, end: $end->getTimestamp() * 1000);
    if (!is_countable($events->getEvents()) || count($events->getEvents()) === 0) {
        throw new Exception('No events found');
    }
    $event = $events->getEvents()[0];
    $eventId = $event->getEventId();
    fwrite(STDOUT, sprintf("\n\nGot events: %s \n", $response->getBody()->getContents()));

    // Test pagination
    $secondPageEvents = $client->searchEvents(2, pagination_key: $events->getPaginationKey(), start: $start->getTimestamp() * 1000, end: $end->getTimestamp() * 1000);
    if (!is_countable($secondPageEvents->getEvents()) || count($secondPageEvents->getEvents()) === 0) {
        throw new Exception('Second page of searchEvents is empty');
    }
} catch (ApiException $e) {
    /** @var ErrorResponse $errorDetails */
    $errorDetails = $e->getErrorDetails();
    $error = $errorDetails->getError();
    fwrite(STDERR, sprintf("\n\nException when calling FingerprintApi->searchEvents or FingerprintApi->searchEventsWithHttpInfo: [%s] %s\n", $error->getCode()->name, $error->getMessage()));

    exit(1);
} catch (Exception $e) {
    fwrite(STDERR, sprintf("\n\nException when calling FingerprintApi->searchEvents or FingerprintApi->searchEventsWithHttpInfo: %s\n", $e->getMessage()));

    exit(1);
}

// FingerprintApi->deleteVisitorData usage example
if ($visitorIdToDelete) {
    try {
        $client->deleteVisitorData($visitorIdToDelete);
        fwrite(STDOUT, "Visitor data deleted");
    } catch (Exception $e) {
        fwrite(STDERR, sprintf("Exception when calling FingerprintApi->deleteVisitorData: %s\n", $e->getMessage()));
        exit(1);
    }
}

// FingerprintApi->getEvent usage example
try {
    /** @var Event $result */
    $event = $client->getEvent($eventId);
    if ($event->getEventId() !== $eventId) {
        throw new Exception('Argument eventId is not equal to deserialized getEventId');
    }
    fwrite(STDOUT, sprintf("\n\nGot event: %s \n", $response->getBody()->getContents()));
} catch (Exception $e) {
    fwrite(STDERR, sprintf("\n\nException when calling FingerprintApi->getEvent: %s\n", $e->getMessage()));

    exit(1);
}

// FingerprintApi->updateEvent usage example
if ($eventIdToUpdate) {
    try {
        $body = new EventUpdate([
            'linked_id' => date('Y-m-d H:i:s'),
        ]);
        $client->updateEvent($eventIdToUpdate, $body);
        fwrite(STDOUT, "Event updated");
    } catch (Exception $e) {
        fwrite(STDOUT, sprintf("\n\nException when calling FingerprintApi->updateEvent: %s\n", $e->getMessage()));
        exit(1);
    }
}

// Call API asynchronously example
$eventPromise = $client->getEventAsyncWithHttpInfo($eventId);
$eventPromise->then(function ($promiseResult) use ($eventId) {
    list($event, $response) = $promiseResult;
    if ($event->getEventId() !== $eventId) {
        throw new Exception('Argument eventId is not equal to deserialized getEventId');
    }
    fwrite(STDOUT, sprintf("\n\nGot async event: %s \n", $response->getBody()->getContents()));
}, function ($exception) {
    fwrite(STDERR, sprintf("\n\nException when calling FingerprintApi->getEventAsync: %s\n", $exception->getMessage()));

    exit(1);
})->wait();

// Webhook verification example
$webhookSecret = 'secret';
$webhookData = 'data';
$webhookHeader = 'v1=1b2c16b75bd2a870c114153ccda5bcfca63314bc722fa160d690de133ccbb9db';
$isValidWebhookSign = WebhookVerifier::isValidWebhookSignature($webhookHeader, $webhookData, $webhookSecret);
if ($isValidWebhookSign) {
    fwrite(STDOUT, "\n\nVerified webhook signature\n");
} else {
    fwrite(STDERR, "\n\nWebhook signature verification failed\n");

    exit(1);
}

// Check that old events still match expected format
try {
    $oldEvents = $client->searchEvents(1, start: $start->getTimestamp() * 1000, end: $end->getTimestamp() * 1000, reverse: true);
    if (!is_countable($oldEvents->getEvents()) || count($oldEvents->getEvents()) === 0) {
        throw new Exception('No old events found');
    }
    $oldEvent = $oldEvents->getEvents()[0];
    $eventIdOld = $oldEvent->getEventId();

    if ($eventId === $eventIdOld) {
        throw new Exception('Old events are identical to new');
    }

    $client->getEvent($eventIdOld);
    fwrite(STDOUT, "\n\nOld events are good\n");
} catch (Exception $e) {
    fwrite(STDERR, sprintf("\n\nException when trying to read old data: %s\n", $e->getMessage()));
}

fwrite(STDOUT, "\n\nChecks passed\n");

exit(0);

<?php

namespace Fingerprint\ServerAPI\Support;

/**
 * Captures the literal request-line a real HTTP client puts on the wire.
 *
 * PSR-7's Uri never normalizes dot-segments, so asserting against
 * $request->getUri()->getPath() cannot reveal that curl collapses a bare
 * '.' or '..' path segment before the bytes leave the process. This spins
 * up a plain TCP listener in a child process and hands back the raw
 * request line it received, so tests can assert on what the transport
 * actually sent rather than what the PHP object model says it sent.
 *
 * @internal
 */
final class RawRequestCapture
{
    private $process;

    /** @var resource */
    private $stdout;

    private string $captureFile;

    private int $port;

    /**
     * @param resource $process
     * @param resource $stdout
     */
    private function __construct($process, $stdout, string $captureFile, int $port)
    {
        $this->process = $process;
        $this->stdout = $stdout;
        $this->captureFile = $captureFile;
        $this->port = $port;
    }

    public static function start(): self
    {
        $port = self::reserveFreePort();
        $captureFile = tempnam(sys_get_temp_dir(), 'fp_raw_req_');
        $listenerScript = __DIR__.'/raw_request_listener.php';

        $process = proc_open(
            [PHP_BINARY, $listenerScript, (string) $port, $captureFile],
            [1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
            $pipes
        );

        if (!\is_resource($process)) {
            unlink($captureFile);

            throw new \RuntimeException('Failed to start raw request listener.');
        }

        foreach ($pipes as $pipe) {
            stream_set_blocking($pipe, false);
        }

        $capture = new self($process, $pipes[1], $captureFile, $port);
        $capture->waitUntilListening();

        return $capture;
    }

    public function baseUri(): string
    {
        return "http://127.0.0.1:{$this->port}";
    }

    /**
     * Returns the raw request line (e.g. "GET /events/. HTTP/1.1")
     * once the listener has accepted and read one request.
     */
    public function requestLine(): ?string
    {
        $deadline = microtime(true) + 2.0;
        while (microtime(true) < $deadline) {
            $contents = @file_get_contents($this->captureFile);
            if (false !== $contents && '' !== $contents) {
                return strtok($contents, "\r\n");
            }
            usleep(10_000);
        }

        return null;
    }

    public function stop(): void
    {
        if (\is_resource($this->process)) {
            proc_terminate($this->process);
            proc_close($this->process);
        }
        if (file_exists($this->captureFile)) {
            unlink($this->captureFile);
        }
    }

    private function waitUntilListening(): void
    {
        $deadline = microtime(true) + 2.0;
        $buffer = '';
        while (microtime(true) < $deadline) {
            $buffer .= (string) fread($this->stdout, 8192);
            if (str_contains($buffer, "READY\n")) {
                return;
            }
            usleep(10_000);
        }

        throw new \RuntimeException('Raw request listener did not start listening in time.');
    }

    private static function reserveFreePort(): int
    {
        $server = stream_socket_server('tcp://127.0.0.1:0', $errno, $errstr);
        if (!\is_resource($server)) {
            throw new \RuntimeException("Failed to reserve a free port: {$errstr}");
        }
        $name = stream_socket_get_name($server, false);
        fclose($server);

        return (int) substr($name, strrpos($name, ':') + 1);
    }
}

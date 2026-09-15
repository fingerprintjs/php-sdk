<?php

namespace Fingerprint\ServerSdk\Test\Support;

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

    /** @var resource */
    private $stderr;

    private string $buffer = '';

    private int $port;

    /**
     * @param resource $process
     * @param resource $stdout
     * @param resource $stderr
     */
    private function __construct($process, $stdout, $stderr)
    {
        $this->process = $process;
        $this->stdout = $stdout;
        $this->stderr = $stderr;
    }

    public static function start(): self
    {
        $process = proc_open(
            [PHP_BINARY, __DIR__.'/raw_request_listener.php'],
            [1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
            $pipes
        );

        if (!\is_resource($process)) {
            throw new \RuntimeException('Failed to start raw request listener.');
        }

        foreach ($pipes as $pipe) {
            stream_set_blocking($pipe, false);
        }

        $capture = new self($process, $pipes[1], $pipes[2]);

        try {
            $ready = $capture->readLine(10.0);
            if (!str_starts_with($ready, 'READY ')) {
                throw new \RuntimeException("Unexpected listener handshake: {$ready}");
            }
            $capture->port = (int) substr($ready, 6);
        } catch (\Throwable $e) {
            $capture->stop();

            throw $e;
        }

        return $capture;
    }

    public function baseUri(): string
    {
        return "http://127.0.0.1:{$this->port}";
    }

    /**
     * Returns the raw request line (e.g. "GET /v4/events/. HTTP/1.1")
     * once the listener has accepted and read one request.
     */
    public function requestLine(): string
    {
        return $this->readLine(10.0);
    }

    public function stop(): void
    {
        if (\is_resource($this->process)) {
            proc_terminate($this->process);
            proc_close($this->process);
        }
    }

    private function readLine(float $timeout): string
    {
        $deadline = microtime(true) + $timeout;

        while (false === ($eol = strpos($this->buffer, "\n"))) {
            $chunk = fread($this->stdout, 8192);
            if (false !== $chunk && '' !== $chunk) {
                $this->buffer .= $chunk;

                continue;
            }
            if (feof($this->stdout)) {
                throw new \RuntimeException('Raw request listener exited early. stderr: '.$this->stderrTail());
            }
            if (microtime(true) >= $deadline) {
                throw new \RuntimeException('Timed out reading from the raw request listener. stderr: '.$this->stderrTail());
            }
            usleep(5000);
        }

        $line = substr($this->buffer, 0, $eol);
        $this->buffer = substr($this->buffer, $eol + 1);

        return rtrim($line, "\r");
    }

    private function stderrTail(): string
    {
        $stderr = (string) @stream_get_contents($this->stderr);

        return '' === $stderr ? '(empty)' : $stderr;
    }
}

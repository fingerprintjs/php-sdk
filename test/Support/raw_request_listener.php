<?php

/**
 * Standalone one-shot TCP listener used by RawRequestCapture.
 *
 * Accepts a single connection, writes the raw request line + headers it
 * received to the given file, replies with a minimal 200 so the client
 * doesn't hang, then exits.
 *
 * @internal
 */
[, $port, $captureFile] = $argv;

$server = stream_socket_server("tcp://127.0.0.1:{$port}", $errno, $errstr);
if (!\is_resource($server)) {
    fwrite(STDERR, "listen failed: {$errstr}\n");

    exit(1);
}

// Signal readiness over stdout rather than via a throwaway TCP connection,
// since this listener only ever accepts a single (the real) connection.
fwrite(STDOUT, "READY\n");
fflush(STDOUT);

$conn = @stream_socket_accept($server, 5);
if (\is_resource($conn)) {
    stream_set_timeout($conn, 2);
    $request = '';
    while (!feof($conn)) {
        $line = fgets($conn);
        if (false === $line) {
            break;
        }
        $request .= $line;
        if ("\r\n" === $line || "\n" === $line) {
            break;
        }
    }
    file_put_contents($captureFile, $request);

    $body = '{}';
    fwrite(
        $conn,
        "HTTP/1.1 200 OK\r\nContent-Type: application/json\r\nContent-Length: ".\strlen($body)."\r\nConnection: close\r\n\r\n".$body
    );
    fclose($conn);
}

fclose($server);

<?php

/**
 * Standalone one-shot TCP listener used by RawRequestCapture.
 *
 * Binds an ephemeral port, announces it on stdout, then accepts a single
 * connection, echoes the raw request line it received back on stdout,
 * replies with a minimal 200 so the client doesn't hang, and exits.
 *
 * @internal
 */
$server = stream_socket_server('tcp://127.0.0.1:0', $errno, $errstr);
if (!\is_resource($server)) {
    fwrite(STDERR, "listen failed: {$errstr}\n");

    exit(1);
}

$name = stream_socket_get_name($server, false);
fwrite(STDOUT, 'READY '.substr($name, strrpos($name, ':') + 1)."\n");
fflush(STDOUT);

$conn = @stream_socket_accept($server, 5);
if (\is_resource($conn)) {
    stream_set_timeout($conn, 2);

    fwrite(STDOUT, rtrim((string) fgets($conn), "\r\n")."\n");
    fflush(STDOUT);

    while (!feof($conn)) {
        $line = fgets($conn);
        if (false === $line || "\r\n" === $line || "\n" === $line) {
            break;
        }
    }

    $body = '{}';
    fwrite(
        $conn,
        "HTTP/1.1 200 OK\r\nContent-Type: application/json\r\nContent-Length: ".\strlen($body)."\r\nConnection: close\r\n\r\n".$body
    );
    fclose($conn);
}

fclose($server);

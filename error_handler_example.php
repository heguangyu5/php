<?php
/**
 * error handler example
 *
 * @see http://mwop.net/blog/on-error-handling-and-closures
 */

$filename = 'fakefile';

set_error_handler(
    function($error, $message = '', $file = '', $line = 0) use ($filename) {
        throw new RuntimeException(sprintf(
            'Error reading file "%s" (in %s@%d): %s',
            $filename, $file, $line, $message
        ), $error);
    },
    E_WARNING
);

$fh = fopen($filename, 'r');

restore_error_handler();

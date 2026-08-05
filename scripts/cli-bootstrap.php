<?php
/**
 * Fail closed when a maintenance script is requested outside PHP CLI.
 */

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

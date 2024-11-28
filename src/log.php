<?php
date_default_timezone_set('Europe/Oslo');
/**
 * Logs a message with a timestamp to a specified log file.
 *
 * @param string $message The message to log.
 * @param string $filePath The path to the log file.
 */
function logMessage(string $message, string $filePath = '../logg/api_log.txt'): void
{
    // Format the log entry
    $timestamp = date('Y-m-d H:i:s'); // Current timestamp
    $logEntry = "[{$timestamp}] {$message}\n";

    // Append the log entry to the file
    file_put_contents($filePath, $logEntry, FILE_APPEND);
}

// Example Usage
logMessage("API call to fetch leads succeeded.");


?>

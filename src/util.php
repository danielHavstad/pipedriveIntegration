<?php

/**
 * Simple general Check for if the response contains valid data.
 *
 * @param mixed $data The API response data.
 * @return bool true if data is valid, false otherwise.
 */
function isValidData($data): bool
{
    return isset($data['data']) && is_array($data['data']);
}


?>

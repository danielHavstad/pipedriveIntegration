<?php
require 'vendor/autoload.php'; 

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

/**
 * Fetch persons from the Pipedrive API.
 *
 * @param Client $client The Guzzle client.
 * @param string $apiKey The Pipedrive API key.
 * @return array|null Array of personss if successful, or null on failure.
 */
function fetchPersons(Client $client, string $apiKey): ?array
{
    try {
        $response = $client->get('persons', [
            'query' => ['api_token' => $apiKey],
        ]);

        $data = json_decode($response->getBody(), true);

        // Check if the response contains valid data
        if (isValidPersonData($data)) {
            return $data['data'];
        }

        return null; 
    } catch (RequestException $e) {
        echo "Request Error: " . $e->getMessage() . "\n";
        return null;
    } catch (Exception $e) {
        echo "An unexpected error occurred: " . $e->getMessage() . "\n";
        return null;
    }
}


//factor out as util
/**
 * Simple general Check for if the response contains valid data.
 *
 * @param mixed $data The API response data.
 * @return bool true if data is valid, false otherwise.
 */
function isValidPersonData($data): bool
{
    return isset($data['data']) && is_array($data['data']);
}


/**
 * Prints the result of fetchPersons from the Pipedrive API.
 *
 * @param array $persons $result from fetchLeads
 * @return void.
 */
function printPersons($persons)
    {
    if ($persons !== null) {
        echo "Fetched " . count($persons) . " leads:\n";
        foreach ($persons as $p) {
            echo "- " . ($p['name'] ?? 'Untitled') . " (ID: " . ($p['id'] ?? 'Unknown') .  ")   \n";
        }
    } else {
        echo "No valid persons found or an error occurred.\n";
    }
}



?>
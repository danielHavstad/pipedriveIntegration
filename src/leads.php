<?php
require 'vendor/autoload.php'; 
require_once 'logMessages.php' ;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

/**
 * Fetch leads from the Pipedrive API.
 *
 * @param Client $client The Guzzle client.
 * @param string $apiKey The Pipedrive API key.
 * @return array|null Array of leads if successful, or null on failure.
 */
function fetchLeads(Client $client, string $apiKey): ?array
{
    try {
        $response = $client->get('leads', [
            'query' => ['api_token' => $apiKey],
        ]);

        $data = json_decode($response->getBody(), true);

        // Check if the response contains valid data
        if (isValidLeadData($data)) {
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


/**
 * Simple general Check for if the response contains valid data.
 *
 * @param mixed $data The API response data.
 * @return bool true if data is valid, false otherwise.
 */
function isValidLeadData($data): bool
{
    return isset($data['data']) && is_array($data['data']);
}


/**
 * Prints the result of fetchLeads from the Pipedrive API.
 *
 * @param array $leads $result from fetchLeads
 * @return void.
 */
function printLeads($leads)
    {
    if ($leads !== null) {
        echo "Fetched " . count($leads) . " leads:\n";
        foreach ($leads as $lead) {
            echo "- " . ($lead['title'] ?? 'Untitled') . " (ID: " . ($lead['id'] ?? 'Unknown') ." orgid: " . ($lead['organization_id'] ?? 'Unknown') .  ")   \n";
        }
    } else {
        echo "No valid leads found or an error occurred.\n";
    }
}



?>
<?php
require 'vendor/autoload.php'; 

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

/**
 * Fetch organization from the Pipedrive API.
 *
 * @param Client $client The Guzzle client.
 * @param string $apiKey The Pipedrive API key.
 * @return array|null Array of organizations if successful, or null on failure.
 */
function fetchOrganizations(Client $client, string $apiKey): ?array
{
    try {
        $response = $client->get('organizations', [
            'query' => ['api_token' => $apiKey],
        ]);

        $data = json_decode($response->getBody(), true);

        // Check if the response contains valid data
        if (isValidData($data)) {
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

/** TODO
 * Creates on organization on Pipedrive using their API.
 *
 * @param Client $client The Guzzle client.
 * @param string $apiKey The Pipedrive API key.
 * @param array $data The array of data needed to make the organization
 * @return array|null
 */
function createOrganization(Client $client, string $apiKey): ?array
{
    try {
        $response = $client->get('organizations', [
            'query' => ['api_token' => $apiKey],
        ]);

        $data = json_decode($response->getBody(), true);

        // Check if the response contains valid data
        if (isValidData($data)) {
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

?>
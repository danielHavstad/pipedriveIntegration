<?php
require 'vendor/autoload.php'; 
require_once 'logMessages.php';

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
        if (isValidOrganizationData($data)) {
            return $data['data'];
        }

        return null;
    } catch (RequestException $e) {
        echo "Request Error: " . $e->getMessage() . "\n";
        logMessage("Request Error: " . $e->getMessage() . "\n");
        return null;
    } catch (Exception $e) {
        echo "An unexpected error occurred: " . $e->getMessage() . "\n";
        logMessage("An unexpected error occurred: " . $e->getMessage() . "\n");
        return null;
    }
}

/**
 * Simple general Check for if the response contains valid data.
 *
 * @param mixed $data The API response data.
 * @return bool true if data is valid, false otherwise.
 */
function isValidOrganizationData($data): bool
{
    return isset($data['data']) && is_array($data['data']);
}


/** TODO
 * Use pipedrive search to find organization by name
 *
 * @param Client $client The Guzzle client.
 * @param string $apiKey The Pipedrive API key.
 * @param string $orgname The name of the organization to search for
 * @return 
 */

 function findOrganizationByName(Client $client, string $apiKey,string $orgName ): ?array
{
    try {
        $searchResponse = $client->get('organizations/search', [
            'query' => [
                'term' => $orgName,
                'fields' => 'name',
                'exact_match' => 'true',
                'api_token' => $apiKey,
            ]
        ]);

        $searchData = json_decode($searchResponse->getBody(), true);

        if (isValidOrganizationData($searchData)) {
            return $searchData['data'];
        }
        return null;
    }catch (RequestException $e) {
        echo "Request Error: " . $e->getMessage() . "\n";
        logMessage("Request Error: " . $e->getMessage() . "\n");

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
 * @param string $orgName The name of the organization to create
 * @return 
 */
function createOrganization(Client $client, string $apiKey, string $orgName)
{
    $searchResult = findOrganizationByName($client, $apiKey,$orgName);
    if (!empty($searchResult['items'])) {
        echo "Organization already exists.\n";
        return null;
    }


    try {
        $response = $client->post('organizations', [
            'form_params' => [
                'name' => $orgName,
                
            ],
            'query' => [
                'api_token' => $apiKey,
            ]
        ]);

        $data = json_decode($response->getBody(), true);
        if ($data['success']) {
            echo "Organization created successfully.\n";
            logMessage("Organization with name" . $orgName .  "created succesfully .\n");

            return $data['data'];
        }

        echo "Failed to create organization.\n";
        logMessage("Failed to create organization with name" . $orgName .  ".\n");
        return null;
        
    } catch (RequestException $e) {
        echo "Request Error: " . $e->getMessage() . "\n";
        logMessage("Request Error: " . $e->getMessage() . "\n");
        return null;
    } catch (Exception $e) {
        echo "An unexpected error occurred: " . $e->getMessage() . "\n";
        logMessage("An unexpected error occurred: " . $e->getMessage() . "\n");

        return null;
    }
}


/**
 * Prints the result of fetchOrganizations from the Pipedrive API.
 *
 * @param array $orgs $result from fetchLeads
 * @return void.
 */
function printOrganizations($orgs)
    {
    if ($orgs !== null) {
        echo "Fetched " . count($orgs) . " organizations:\n";
        foreach ($orgs as $org) {
            echo "- " . ($org['name'] ?? 'Untitled') . " (ID: " . ($org['id'] ?? 'Unknown') ." orgid: " . ($org['organization_id'] ?? 'Unknown') .  ")   \n";
        }
    } else {
        echo "No valid organizations found or an error occurred.\n";
    }
}

?>
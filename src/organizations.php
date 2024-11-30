<?php
require_once 'vendor/autoload.php'; 
require_once 'logMessages.php';
require_once 'util.php';

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
        logMessage("Request Error: " . $e->getMessage() . "\n");
        return null;
    } catch (Exception $e) {
        echo "An unexpected error occurred: " . $e->getMessage() . "\n";
        logMessage("An unexpected error occurred: " . $e->getMessage() . "\n");
        return null;
    }
}




/*
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

        if (isValidData($searchData)) {
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

/*
 * Creates on organization on Pipedrive using their API.
 *
 * @param Client $client The Guzzle client.
 * @param string $apiKey The Pipedrive API key.
 * @param string $orgName The name of the organization to create
 * @return array? $orgData data of the newly created or already existing Organization, otherwise null
 */
function createOrganization(Client $client, string $apiKey, string $orgName): ?array
{
    $searchResult = findOrganizationByName($client, $apiKey,$orgName);
    if (!empty($searchResult['items'])) {
        echo "Organization already exists.\n";
        return $searchResult['items'][0]['item'];
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
            $orgId = $data['data']['id']; //  Get organization ID of newly created organization
            echo "Organization created successfully. ID: {$orgId}\n";            
            logMessage("Organization with name '{$orgName}' and ID {$orgId} created successfully.\n");

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
<?php
require_once 'vendor/autoload.php'; 
require_once 'logMessages.php' ;
require_once 'util.php';


use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

//lead custom fields

/**
 * Maps a string value of custom field housing_type to its integer representation.
 *
 * @param string $housing_type The housing_type string to map.
 * @return int|null The corresponding integer value or null if not found.
 */
function map_housing_type_string(string $housing_type): ?int 
{
    $housingType = strtolower(trim($housing_type));
    $housingTypeChoices = [
        'enebolig' => 33,
        'leilighet' => 34,
        'tomannsbolig' => 35,
        'rekkehus' => 36,
        'hytte' => 37,
        'annet' => 38,
    ];
    return $housingTypeChoices[$housingType] ?? null; // Return null if not found
}

/**
 * Maps a string value of custom field deal_type to its integer representation.
 *
 * @param string $deal_type The deal_type string to map.
 * @return int|null The corresponding integer value or null if not found.
 */
function map_deal_type_string(string $deal_type): ?int 
{
    $dealType = strtolower(trim($deal_type));
    $dealTypeChoices = [
        'alle strømavtaler er aktuelle' => 39,
        'fastpris' => 40,
        'spotpris' => 41,
        'kraftforvaltning' => 42,
        'annen avtale/vet ikke' => 43,
    ];
    return $dealTypeChoices[$dealType] ?? null; // Return null if not found
}

/**
 * Generates a hash-based title for a lead.
 *
 * @param int $personId The ID of the associated person.
 * @param int $orgId The ID of the associated organization.
 * @param string $housingType The housing type (e.g., enebolig, leilighet).
 * @param string $dealType The deal type (e.g., spotpris, fastpris).
 * @return string The generated hash title.
 */
function generateLeadTitle(int $personId, int $orgId, int $propertySize, string $housingType, string $dealType): string
{
    $dataString = "{$personId}-{$orgId}-{$housingType}-{$propertySize}-{$dealType}";
    return 'Lead-' . hash('sha256', $dataString); // Generate SHA-256 hash
}




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


/**
 * Find a lead in the Pipedrive API by title.
 *
 * @param Client $client The Guzzle client.
 * @param string $apiKey The Pipedrive API key.
 * @param string $leadTitle The title of the lead to search for.
 * @return array|null Array of leads if successful, or null on failure.
 */
function findLeadByTitle(Client $client, string $apiKey, string $leadTitle): ?array
{
    try {
        $searchResponse = $client->get('leads/search', [
            'query' => [
                'term' => $leadTitle,
                'fields' => 'title',
                'exact_match' => 'true',
                'api_token' => $apiKey,
            ]
        ]);

        $searchData = json_decode($searchResponse->getBody(), true);

        if (isValidData($searchData)) {
            return $searchData['data'];
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
 * Creates a lead in Pipedrive and associates it with a person and an organization.
 *
 * @param Client $client The Guzzle client.
 * @param string $apiKey The Pipedrive API key.
 * @param string $leadTitle The title of the lead.
 * @param int $personId The ID of the person to associate with the lead.
 * @param int $orgId The ID of the organization to associate with the lead.
 * @param string $housingType The housing type (e.g., enebolig, leilighet).
 * @param string $dealType The deal type (e.g., spotpris, fastpris).
 * @return array|null The created lead data, or null on failure.
 */
function createLead(
    Client $client,
    string $apiKey,
    int $orgId,
    int $personId,
    string $housingType,
    int $propertySize,
    string $dealType
): ?array {

    //create lead title hash
    $leadTitle = generateLeadTitle($personId,$orgId,$propertySize,$housingType,$dealType);

    $searchResult = findLeadByTitle($client,$apiKey,$leadTitle);
    if (!empty($searchResult['items'])) {
        echo "Lead already exists.\n";
        return $searchResult['items'][0]['item'];
    }

    // Map custom field values
    $housingTypeValue = map_housing_type_string($housingType);
    $dealTypeValue = map_deal_type_string($dealType);

    if ($housingTypeValue === null || $dealTypeValue === null) {
        echo "Invalid custom field value(s) for leads provided.\n";
        return null;
    }

    try {
        // Send POST request to create the lead
        $response = $client->post('leads', [
            'form_params' => [
                'title' => $leadTitle,
                'person_id' => $personId,
                'org_id' => $orgId,
                '9cbbad3c5d83d6d258ef27db4d3784b5e0d5fd32' => $housingTypeValue, // housing_type custom field
                '7a275c324d7fbe5ab62c9f05bfbe87dad3acc3ba' => $propertySize, //property_size custom field
                'cebe4ad7ce36c3508c3722b6e0072c6de5250586' => $dealTypeValue,   // deal_type custom field
            ],
            'query' => [
                'api_token' => $apiKey,
            ]
        ]);

        $data = json_decode($response->getBody(), true);

        if ($data['success']) {
            echo "Lead with title: " . $leadTitle . "created successfully.\n";
            logMessage("Lead with title: " . $leadTitle . "created successfully.\n");
            return $data['data'];
        }

        echo "Failed to create lead.\n";
        return null;
    } catch (RequestException $e) {
        $error = "Request Error: " . $e->getMessage() . "\n";
        echo "Request Error: " . $e->getMessage() . "\n";
        if ($e->hasResponse()) {
            echo "Response Body: " . $e->getResponse()->getBody() . "\n";
            $error += "Response Body: " . $e->getResponse()->getBody() . "\n";
        }
        logMessage($error);
        return null;
    } catch (Exception $e) {
        echo "An unexpected error occurred: " . $e->getMessage() . "\n";
        logMessage("An unexpected error occurred: " . $e->getMessage());
        return null;
    }
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
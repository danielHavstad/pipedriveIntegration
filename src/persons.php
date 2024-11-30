<?php
require_once 'vendor/autoload.php'; 
require_once 'util.php';


use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

//custom fields person

/**
 * Maps a string value of custom field contact_type to its integer representation.
 *
 * @param string $contact_type the contact_type string to map.
 * @return int|null The corresponding integer value or null if not found.
 */
function map_contact_type_string(string $contact_type): ?int 
{
    $contactType = strtolower(trim($contact_type));
    $contactTypeChoices = [
        'privat' => 30,
        'borettslag' => 31,
        'bedrift' => 32,
    ];
    return $contactTypeChoices[$contactType] ?? null;
}



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
 * Find a person in the Pipedrive API by name.
 *
 * @param Client $client The Guzzle client.
 * @param string $apiKey The Pipedrive API key.
 * @param string $personName The name of the person to search for.
 * @return array|null Array of personss if successful, or null on failure.
 */
function findPersonByName(Client $client, string $apiKey,string $personName ): ?array
{
    try {
        $searchResponse = $client->get('persons/search', [
            'query' => [
                'term' => $personName,
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


/**
 * Creates a person in Pipedrive and associates them with an organization, including setting a custom field.
 *
 * @param Client $client The Guzzle client.
 * @param string $apiKey The Pipedrive API key.
 * @param int $organizationId The ID of the organization to associate the person with.
 * @param string $name The name of the person.
 * @param string $email The email of the person.
 * @param string $phone The phone number of the person.
 * @param string $contactType The contact type (privat, borettslag, bedrift).
 * @return array|null The created person data, or null on failure.
 */
function createPerson(
    Client $client,
    string $apiKey,
    int $organizationId,
    string $name,
    string $email,
    string $phone,
    string $contactType
): ?array {

    $searchResult = findPersonByName($client, $apiKey,$name);
    if (!empty($searchResult['items'])) {
        echo "Person already exists.\n";
        return $searchResult['items'][0]['item'];
    }
 
    // Mapping contact type choices to their IDs
    $contactTypeValue = map_contact_type_string($contactType);

    if ($contactTypeValue == null) {
        echo "Invalid contact type provided: {$contactType}\n";
        return null;
    }

    try {
        // Send POST request to create the person
        $response = $client->post('persons', [
            'form_params' => [
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'org_id' => $organizationId, // Associate with the organization
                'fd460d099264059d975249b20e071e05392f329d' => $contactTypeValue, // Set custom field
            ],
            'query' => [
                'api_token' => $apiKey,
            ]
        ]);

        $data = json_decode($response->getBody(), true);

        if ($data['success']) {
            echo "Person created successfully.\n";
            return $data['data'];
        }

        echo "Failed to create person.\n";
        return null;
    } catch (RequestException $e) {
        // Handle HTTP errors
        $error = "Request Error: " . $e->getMessage() . "\n";
        echo "Request Error: " . $e->getMessage() . "\n";
        if ($e->hasResponse()) {
            echo "Response Body: " . $e->getResponse()->getBody() . "\n";
            $error .= "Response Body: " . $e->getResponse()->getBody() . "\n";
        }
        logMessage($error);
        return null;
    } catch (Exception $e) {
        // Handle other errors
        echo "An unexpected error occurred: " . $e->getMessage() . "\n";
        logMessage("An unexpected error occurred: " . $e->getMessage() . "\n");
        return null;
    }
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
        echo "Fetched " . count($persons) . " persons:\n";
        foreach ($persons as $p) {
            echo "- " . ($p['name'] ?? 'Untitled') . " (ID: " . ($p['id'] ?? 'Unknown') .  ")   \n";
        }
    } else {
        echo "No valid persons found or an error occurred.\n";
    }
}



?>
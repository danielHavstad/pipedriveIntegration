<?php
require 'vendor/autoload.php'; 
require 'leads.php';

// Pipedrive API details
$domain = 'nettbureauasdevelopmentteam'; 
$apiKey = 'cc8b7ad043662da5fc83b3359789daea6cf21c8a'; 

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

// pipedrive API
$domain = 'nettbureauasdevelopmentteam'; 
$apiKey = 'cc8b7ad043662da5fc83b3359789daea6cf21c8a'; 

// create a client to access API using guzzle
$client = new Client([
    'base_uri' => "https://{$domain}.pipedrive.com/v1/",
    'timeout'  => 30.0, // timeout in seconds
]);









$leads = fetchLeads($client, $apiKey);
$organizations = fetchOrganizations($client, $apiKey);

//printLeads($leads);

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

printOrganizations($organizations)

?>
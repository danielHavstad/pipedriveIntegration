<?php
require_once 'vendor/autoload.php'; 

require_once 'leads.php';
require_once 'organizations.php' ;
require_once 'persons.php';

require_once 'data.php' ;

// Pipedrive API details nettbureau (closed)
$domain = 'nettbureauasdevelopmentteam'; 
$apiKey = '2dd6f0157e0f6eabcf1de7d287bb20251ee61097'; 

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

// pipedrive API when using personal account
//$domain = 'api'; 
//$apiKey = '9008c4f52619af686e61587a4a07723736c4730f'; 

// create a client to access API using guzzle
$client = new Client([
    'base_uri' => "https://{$domain}.pipedrive.com/v1/",
    'timeout'  => 30.0, // timeout in seconds
]);

$organizationName = "LoremIpsumInc";



$testData = loadTestData();

/**
 * Creates a organization, person, and lead (if not exists) in Pipedrive.
 *
 * @param Client $client The Guzzle client.
 * @param string $apiKey The Pipedrive API key.
 * @param string $organizationName The name of the organization to make.
 * @param array $data data in format displayed in the task description pdf.
 */
function integrateLeadOnPipedrive(Client $client,string $apiKey, string $organizationName, array $data):void
{
    //create organization
    $createOrgResponse = createOrganization($client,$apiKey, $organizationName);
    //printing responses so whomever runs this can verify execution, comment out print sentences if youd like.
    print_r($createOrgResponse);
    $orgid = $createOrgResponse['id'];
    //create person connected to organization
    $createPersonResponse = createPerson($client, $apiKey, $orgid, $data['name'], $data['email'], $data['phone'], $data['contact_type']);
    print_r($createPersonResponse);
    $personid = $createPersonResponse['id'];
    //create lead connected to person and organization
    $createLeadResponse = createLead($client, $apiKey, $orgid, $personid, $data['housing_type'], $data['property_size'], $data['deal_type']);
    print_r($createLeadResponse);
}


$leads = fetchLeads($client, $apiKey);
$organizations = fetchOrganizations($client, $apiKey);
$persons = fetchPersons($client, $apiKey);
printOrganizations($organizations);
printPersons($persons);
printLeads($leads);

integrateLeadOnPipedrive($client, $apiKey,$organizationName,$testData);



?>
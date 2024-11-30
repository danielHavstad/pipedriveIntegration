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

$organizationName = "LoremIpsumOrg";



$testData = loadTestData();



$leads = fetchLeads($client, $apiKey);
$organizations = fetchOrganizations($client, $apiKey);
$persons = fetchPersons($client, $apiKey);
printOrganizations($organizations);
printPersons($persons);
printLeads($leads);

$createOrgResponse = createOrganization($client,$apiKey, $organizationName);
//print_r($createOrgResponse);

$orgid = $createOrgResponse['id'];
//print_r($apiKey);
$createPersonResponse = createPerson($client, $apiKey, $orgid, $testData['name'], $testData['email'], $testData['phone'], $testData['contact_type']);

$personid = $createPersonResponse['id'];
//print_r( $createPersonResponse );
$createLeadResponse = createLead($client, $apiKey, $orgid, $personid, $testData['housing_type'], $testData['property_size'], $testData['deal_type']);
print_r($createLeadResponse);
?>
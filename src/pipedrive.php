<?php
require 'vendor/autoload.php'; 
require 'leads.php';
require 'organizations.php' ;

// Pipedrive API details nettbureau (closed)
//$domain = 'nettbureauasdevelopmentteam'; 
//$apiKey = 'cc8b7ad043662da5fc83b3359789daea6cf21c8a'; 

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

// pipedrive API when using personal account
$domain = 'api'; 
$apiKey = '9008c4f52619af686e61587a4a07723736c4730f'; 

// create a client to access API using guzzle
$client = new Client([
    'base_uri' => "https://{$domain}.pipedrive.com/v1/",
    'timeout'  => 30.0, // timeout in seconds
]);

$organizationName = "LoremIpsumOrg";







//$leads = fetchLeads($client, $apiKey);
$organizations = fetchOrganizations($client, $apiKey);
printOrganizations($organizations);

//printLeads($leads);

createOrganization($client,$apiKey, $organizationName);


?>
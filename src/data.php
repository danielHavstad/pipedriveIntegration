<?php


$datafile = '../data/testdata.json';


/**
 * Load test data from json file
 *  * @return array $data
 */
function loadTestData(): array
{
    global $datafile;
    $readJson = file_get_contents( $datafile);
    // Decode the JSON string into a PHP array
    $data = json_decode($readJson, true); // Pass `true` to get an associative array

    //print_r($data);


    return $parsedData;
}


loadTestData()




// // Access fields
// echo "Name: " . $parsedData['name'] . "\n";
// echo "Phone: " . $parsedData['phone'] . "\n";
// echo "Email: " . $parsedData['email'] . "\n";


?>
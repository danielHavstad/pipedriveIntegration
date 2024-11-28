<?php


/**
 * Load test data from json file
 *  * @param string $datafile path to testdata.
 *  * @return array $data
 */
function loadTestData(string $datafile = '../data/testdata.json'): array
{
    $readJson = file_get_contents( $datafile);
    // Decode the JSON string into a PHP array
    $data = json_decode($readJson, true); // Pass `true` to get an associative array

    //print_r($data);


    return $data;
}


loadTestData()




// // Access fields
// echo "Name: " . $parsedData['name'] . "\n";
// echo "Phone: " . $parsedData['phone'] . "\n";
// echo "Email: " . $parsedData['email'] . "\n";


?>
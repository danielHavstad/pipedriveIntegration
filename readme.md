## Installation & how to run
- note i am running ubuntu linux, windows instructions might differ.
- have PHP 8.0+

First of all open up a terminal in the project folder.
Change directory to src:
-   $cd src

I use guzzle library for api calls.
Install by running: 
-   $ composer install 

### IMPORTANT: before running pipedrive.php add the api key to the following variable on line 13 (ca.).
Will look like this: $apiKey = ''; 

To run script that will create on pipedrive (if not exists) an organisation, a person, and a lead connected to person and organisation, based on testdata.json.
Execute the following commands in terminal
-   $php pipedrive.php

### update data in testdata.json to upload more leads

## Project Structure

- src/ #php source files
    -   data.php # functions pertaining to retrieving/reformating test data
    -   logMessages.php # function for logging
    -   util.php # misc utility functions
    -   organizations.php # functions for organization api calls
    -   persons.php # functions for person api calls
    -   leads.php # functions for lead api calls

    -   pipedrive.php # main entry point, sets up connection client, and completes the task of creating a lead as stated in task description.
- data/
    -   test_data.json slightly modified testdata from task description (swapped name)
- logg/ #log file generates here

## Future work
Listing here features i thought of but didnt want to spend time implementing for this limited scope project.

Make things async to improve latency, disregarded for now since for task i need for example the creation of organization, before i create person, and so on, and its easier and more readable to just not handle promises.

Making api call provider classes, though in some sense this is done by the official php-client so id be reinventing the wheel, maybe i am. setting up the integration itself as pipeline pattern. Creating some priority queue job scheduler, or just retry mechanisms for certain error codes of failed api calls.

Log files with limited timespan, one per day or such. log file backup protocol, could even use blockchain technology (hashchain (merkle tree)) to verify log data has not since been tampered with. Use elastic search to index logs and monitor for bad behaviour.

does not handle case where person might want to be connected to different organizations.

- Daniel S. H.
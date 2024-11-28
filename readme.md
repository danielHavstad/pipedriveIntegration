todo:
create function view org by id and or name
create function view lead by id and or name

createOrganization with post
check Personfields for custom field hashes // dont need to do is in task description pdf as id
create person with post call, consider custom field and linking to organization
check dealfields for lead custom fields // dont need to do is in task description pdf as id
create function for viewing persons
create function view person by id and or name //can use search person or thing functionality to check for person/org already created
create deal with use custom field and give deal person id and org id to link with
 
make good test data
find way parse test data with functions

add error handling they want

maybe use put instead of post if post target already exist

make log function that that writes message to the end of a log text file

rewrite this file for instructions on how to run, and how code is organised

org havstadmill


## Future work
Listing here features i thought of but didnt want to spend time implementing for this limited scope project.

Making api call provider classes, setting up the integration itself as pipeline pattern.

Log files with limited timespan, one per day or such. log file backup protocol, could even use blockchain technology (hashchain (merkle tree)) to verify log data has not since been tampered with.
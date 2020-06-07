<?php

//Define database variables
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'imt3851_assign2');

// Create connection to localhost
$connection = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

//Check the connection
if($connection === false) {
    die("Failed to connect: " . mysqli_connect_error());
}

?>
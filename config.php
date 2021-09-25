<?php
/* Database credentials. Assuming you are running MySQL
server with default setting (user 'root' with no password) */
define('DB_SERVER', 'sql4.freesqldatabase.com');
define('DB_USERNAME', 'sql4440065');
define('DB_PASSWORD', '9ii7MMMfAN');
define('DB_NAME', 'sql4440065');
date_default_timezone_set("europe/lisbon");
/* Attempt to connect to MySQL database */
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if($link === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
?>

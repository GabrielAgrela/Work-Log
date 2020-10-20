<?php
/* Database credentials. Assuming you are running MySQL
server with default setting (user 'root' with no password) */
define('DB_SERVER', 'remotemysql.com');
define('DB_USERNAME', '5omaMAZ0dZ');
define('DB_PASSWORD', 'WHzYLTfC4I');
define('DB_NAME', '5omaMAZ0dZ');
date_default_timezone_set("europe/lisbon");
/* Attempt to connect to MySQL database */
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if($link === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
?>

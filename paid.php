<?php
// Initialize the session
session_start();
require_once "config.php";

// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true)
{
    header("location: login.php");
    exit;
}
$to = "100cabessa@gmail.com";
$subject = "My subject";
$txt = "Hello world!";
$headers = "From: 100cabessa@gmail.com" . "\r\n";

mail($to,$subject,$txt,$headers);
/*$sql = "UPDATE worklog SET paid = 1 where id_user = ".$_SESSION['id'];
if($stmt = mysqli_prepare($link, $sql))
{

	if(mysqli_stmt_execute($stmt))
	{
		echo "success";
	} else{
		echo "Something went wrong. sorry";
	}
	mysqli_stmt_close($stmt);
}*/
?>

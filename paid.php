<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
// Initialize the session
session_start();
require_once "config.php";

// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true)
{
    header("location: login.php");
    exit;
}


$to = '100cabessa@gmail.com';
$subject = 'Aliens Abducted Me - Abduction Report';
$msg = " was abducted  and was gone for.\n" .
"Number of aliens: \n" .
"Alien description: \n" .
"What they did: \n" .
"Fang spotted: \n" .
"Other comments: ";

$mail = new PHPMailer\PHPMailer\PHPMailer();
$mail->  SMTPDebug=2;
$mail ->  isSMTP();
$mail ->  Host='smtp.gmail.com';
$mail ->  SMTPAuth =true;
$mail ->  Username='ithrowthisaway1233321@gmail.com';
$mail ->  Password= '123456123456Aa';
$mail ->  SMTPSecure ='tls';
$mail ->  Port =587;

$mail ->  setFrom($to);
$mail ->  addAddress($to);

$mail ->  addReplyTo($to);
$mail ->  Subject=$subject;
$mail ->  Body=$msg;
$mail ->  send();
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

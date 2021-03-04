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


$mail = new PHPMailer(true);

//Send mail using gmail
if($send_using_gmail){
    $mail->IsSMTP(); // telling the class to use SMTP
    $mail->SMTPAuth = true; // enable SMTP authentication
    $mail->SMTPSecure = "ssl"; // sets the prefix to the servier
    $mail->Host = "smtp.gmail.com"; // sets GMAIL as the SMTP server
    $mail->Port = 465; // set the SMTP port for the GMAIL server
    $mail->Username = "ithrowthisaway1233321@gmail.com"; // GMAIL username
    $mail->Password = "123456123456Aa"; // GMAIL password
}

//Typical mail data
$mail->AddAddress("100cabessa@gmail.com", "jorge");
$mail->SetFrom("100cabessa@gmail.com", "teste");
$mail->Subject = "My Subject";
$mail->Body = "Mail contents";

try{
    $mail->Send();
    echo "Success!";
} catch(Exception $e){
    //Something went bad
    echo "Fail - " . $mail->ErrorInfo;
}
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

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
echo $_SESSION["username"];

?>

<html>
	<head>
		<meta charset="UTF-8">
		<title>Login</title>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
		<style type="text/css">
		body{ font: 14px sans-serif; }
		.wrapper{ width: 350px; padding: 20px; }
		</style>
	</head>
	<body>
		<table border = "1">
		<tr>
			<td>id</td>
			<td>descrição</td>
			<td>inicio</td>
			<td>fim</td>
			<td>tempo total</td>
		</tr>
		<?php if($_SERVER['REQUEST_METHOD'] == "POST" and $_POST['startWork']=="start") :?>
			<form action="index.php" method="post">
				<input type="submit" name="startWork" value="stop" />
			</form>
		<?php else : ?>
			<form action="index.php" method="post">
				<input type="submit" name="startWork" value="start" />
			</form>
		<?php endif; ?>
			<?php
			$sql = "SELECT id, start, finish FROM worklog WHERE id_user = ?";

			if($stmt = mysqli_prepare($link, $sql)){
				// Bind variables to the prepared statement as parameters
				mysqli_stmt_bind_param($stmt, "i", $_SESSION["id"]);


				// Attempt to execute the prepared statement
				if(mysqli_stmt_execute($stmt)){
					// Store result
					mysqli_stmt_store_result($stmt);

					// Check if username exists, if yes then verify password
					if(mysqli_stmt_num_rows($stmt) >= 1)
					{
						mysqli_stmt_bind_result($stmt, $id, $start, $finish);
						while (mysqli_stmt_fetch($stmt))
						{
							echo "<tr>";
							echo "<td>".$id."</td>";
							echo "<td>coisando coisas</td>";
							echo "<td>".$start."</td>";
							echo "<td>".$finish."</td>";
							$datetime1 = strtotime($start);
							$datetime2 = strtotime($finish);
							$secs = $datetime2 - $datetime1;// == <seconds between the two times>
							$minutes = $secs / 60;
							echo "<td>".round($minutes)."</td>";
							echo "</tr>";
						}
					}
					else
					{
						echo"1";
					}
				} else{
					echo "Oops! Something went wrong. Please try again later.";
				}

				// Close statement
				mysqli_stmt_close($stmt);
			}
 			?>
		</table>
	</body>
</html>
<?php
    if($_SERVER['REQUEST_METHOD'] == "POST" and $_POST['startWork']=="start")
    {
        $sql = "INSERT INTO worklog (id_user,start, finish) VALUES (?, ?, ?)";

        if($stmt = mysqli_prepare($link, $sql))
		{
			$datetemp = date('Y-m-d H:i:s');
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "iss", $_SESSION["id"],$datetemp, $datetemp);

            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt))
			{
                echo"contanto";
            } else{
                echo "Something went wrong. Please try again later.";
            }
            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
	if($_SERVER['REQUEST_METHOD'] == "POST" and $_POST['startWork']=="stop")
    {
		$datetemp = date('Y-m-d H:i:s');

        $sql = "UPDATE worklog SET finish = ?, finished = 1 WHERE finished = 0 ORDER BY id DESC LIMIT 1";
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $datetemp);


            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                echo "começar";
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }

?>

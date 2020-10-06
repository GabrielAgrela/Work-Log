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
echo "Olá, ".$_SESSION["username"].".";
echo "<br>";
if($_SERVER['REQUEST_METHOD'] == "POST" and $_POST['startWork']=="start")
{

	$sql = "SELECT * FROM worklog WHERE finished = 0 AND id_user = ? ORDER BY id DESC LIMIT 1";
	if($stmt = mysqli_prepare($link, $sql))
	{
		 mysqli_stmt_bind_param($stmt, "i", $_SESSION["id"]);
		if(mysqli_stmt_execute($stmt))
		{
			/* store result */
			mysqli_stmt_store_result($stmt);

			if(mysqli_stmt_num_rows($stmt) == 0)
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
						echo "";
					} else{
						echo "Something went wrong. Please try again later.";
					}
					// Close statement
					mysqli_stmt_close($stmt);
				}
			}
		}
	}
}
if($_SERVER['REQUEST_METHOD'] == "POST" and $_POST['startWork']=="stop")
{
	$datetemp = date('Y-m-d H:i:s');

	$sql = "UPDATE worklog SET finish = ?, finished = 1 WHERE finished = 0 and id_user = ? ORDER BY id DESC LIMIT 1";
	if($stmt = mysqli_prepare($link, $sql))
	{
		// Bind variables to the prepared statement as parameters
		mysqli_stmt_bind_param($stmt, "si", $datetemp, $_SESSION['id'] );

		// Attempt to execute the prepared statement
		if(mysqli_stmt_execute($stmt))
		{
			echo "";
		} else{
			echo "Oops! Something went wrong. Please try again later.";
		}

		// Close statement
		mysqli_stmt_close($stmt);
	}
	else {
		echo "";
	}
}
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

		<table class = "table">
		<thead>
		<tr>
			<th scope="col">#</th>
			<th scope="col">descrição</th>
			<th scope="col">inicio</th>
			<th scope="col">fim</th>
			<th scope="col">tempo total</th>
		</tr>
		</thead>
		<?php if($_SERVER['REQUEST_METHOD'] == "POST" and $_POST['startWork']=="start") :?>
			<form action="index.php" method="post">
				<input type="submit" name="startWork" value="stop" />
			</form>
		<?php else : ?>
			<form action="index.php" method="post">
				<input type="submit" name="startWork" value="start" />
			</form>
		<?php endif; ?>
			<br>
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
							echo "<th scope='row'>".$id."</th>";
							echo "<td>coisando coisas</td>";
							echo "<td>".date('d-m-Y H:i', strtotime($start))."</td>";
							if ($start == $finish)
								echo "<td>ONGOING</td>";
							else
								echo "<td>".date('d-m-Y H:i', strtotime($finish))."</td>";
							$datetime1 = strtotime($start);
							$datetime2 = strtotime($finish);
							$secs = $datetime2 - $datetime1;// == <seconds between the two times>
							$minutes = $secs / 60;
							echo "<td>".round($minutes)." m</td>";
							echo "</tr>";
						}
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


?>

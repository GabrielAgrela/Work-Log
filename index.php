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

//total minutes worked, incremented by table
$totalMinutes=0;

//welcome user
echo "<h1 style='margin-bottom: 1%'>Olá, ".$_SESSION["username"]."!</h1>";

// if button/form start pressed check if there is unfinished worklogs in this user, if so, don't do anything, if there isn't insert new worklog with temporary "finish" parameter
if($_SERVER['REQUEST_METHOD'] == "POST" and $_POST['startWork']=="start")
{
	//select all unfinished worklogs from this user
	$sql = "SELECT * FROM worklog WHERE finished = 0 AND id_user = ? ORDER BY id DESC LIMIT 1";
	if($stmt = mysqli_prepare($link, $sql))
	{
		mysqli_stmt_bind_param($stmt, "i", $_SESSION["id"]);
		if(mysqli_stmt_execute($stmt))
		{
			mysqli_stmt_store_result($stmt);
			//if there is no unfinished worklogs, insert a new one
			if(mysqli_stmt_num_rows($stmt) == 0)
			{
				$sql = "INSERT INTO worklog (id_user,start, finish) VALUES (?, ?, ?)";
				if($stmt = mysqli_prepare($link, $sql))
				{
					$datetemp = date('Y-m-d H:i:s');
					//inserting "finish" parameter as a temporary (updated when user finishes this worklog)
					mysqli_stmt_bind_param($stmt, "iss", $_SESSION["id"],$datetemp, $datetemp);
					if(mysqli_stmt_execute($stmt))
					{
						echo "";
					} else{
						echo "Something went wrong. sorry";
					}
					mysqli_stmt_close($stmt);
				}
			}
		}
	}
}

//if button/form pressed is "stop" then update latest's user "finish" parameter worklog with the datetime of pressing the button
if($_SERVER['REQUEST_METHOD'] == "POST" and $_POST['startWork']=="stop")
{
	$datetemp = date('Y-m-d H:i:s');
	$sql = "UPDATE worklog SET finish = ?, finished = 1, description = ? WHERE finished = 0 and id_user = ? ORDER BY id DESC LIMIT 1";
	if($stmt = mysqli_prepare($link, $sql))
	{
		mysqli_stmt_bind_param($stmt, "ssi", $datetemp,$_POST['description'], $_SESSION['id'] );
		if(mysqli_stmt_execute($stmt))
		{
			echo "";
		}
		else
		{
			echo "Oops! Something went wrong. Please try again later.";
		}
		mysqli_stmt_close($stmt);
	}
	else
	{
		echo "";
	}
}
?>

<!-- START HTML-->
<html>
	<head>
		<meta charset="UTF-8">
		<title>workLogs</title>
		<link rel="icon" type="image/png" href="https://i.imgur.com/UDAqk7t.png" />
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
		<style type="text/css">
		body{ font: 14px sans-serif; }
		.wrapper{ width: 350px; padding: 20px; }
		</style>
	</head>
	<body>
		<!-- if button/form previously pressed was "start" then change to "stop"-->
		<?php if($_SERVER['REQUEST_METHOD'] == "POST" and $_POST['startWork']=="start") :?>
			<form action="index.php" method="post">
				<input class="btn btn-primary" type="submit" name="startWork" value="stop" />
		<!-- if button/form previously pressed was "stop" then change to "start"-->
		<?php else : ?>
			<form action="index.php" method="post">
				<input class="btn btn-primary" type="submit" name="startWork" value="start" />
		<?php endif; ?>

			<br>
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
		<?php
			//select worklog data from this user
			$sql = "SELECT id, description, start, finish FROM worklog WHERE id_user = ?";
			//for each row of data in worklog table, write a row in the html table
			if($stmt = mysqli_prepare($link, $sql))
			{
				mysqli_stmt_bind_param($stmt, "i", $_SESSION["id"]);
				if(mysqli_stmt_execute($stmt))
				{
					mysqli_stmt_store_result($stmt);
					if(mysqli_stmt_num_rows($stmt) >= 1)
					{
						$i=0;//checker to see if number of current itteration == last table row
						mysqli_stmt_bind_result($stmt, $id,$description, $start, $finish);
						while (mysqli_stmt_fetch($stmt))
						{
							$i++;
							echo "<tr>";
							echo "<th scope='row'>".$id."</th>";

							//user can only input description on an unfinished worklog
							if ($_POST['startWork']=="start" && $i == mysqli_stmt_num_rows($stmt))
								echo "<td><input type='text' name='description' value='$description'></td>";
							else
								echo "<td>$description</td>";

							echo "<td>".date('d-m-Y H:i', strtotime($start))."</td>";

							// if start == finish, then it means the work isn't finished
							if ($start == $finish)
								echo "<td>ONGOING</td>";
							else
								echo "<td>".date('d-m-Y H:i', strtotime($finish))."</td>";//formating datetime to remove seconds

							//operations to get the time difference between finish worklog and start worklog in minutes (work time)
							$datetime1 = strtotime($start);
							$datetime2 = strtotime($finish);
							$secs = $datetime2 - $datetime1;
							$minutes = $secs / 60;
							echo "<td>".round($minutes)." m</td>";
							$totalMinutes=$totalMinutes + round($minutes);
							echo "</tr>";
						}
					}
				}
				else
				{
					echo "Oops! Something went wrong. Please try again later.";
				}
					mysqli_stmt_close($stmt);
			}
			?>
		</table>
		</form>
		<h2 style="text-align:right; margin-right:2%; margin-bottom:2%;">Total work: <?php echo $totalMinutes." m (".round($totalMinutes/60)." h)";?></h2>
	</body>
</html>
<?php
?>

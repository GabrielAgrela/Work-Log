<?php
// Initialize the session
session_start();
require_once "config.php";

// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["admin"] !== true)
{
    header("location: logout.php");
    exit;
}

//total minutes worked, incremented by table
$totalMinutes=0;

//welcome user
echo "<h1 style='margin-bottom: 1%'>Olá, ".$_SESSION["username"]."!</h1>";

?>

<!-- START HTML-->
<html>
	<head>
		<meta charset="UTF-8">
		<title>Index</title>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
		<style type="text/css">
	        body{ font: 18px sans-serif;!important position: relative; padding: 5%; }
	        .wrapper{ width: 350px; padding: 20px; }
	    </style>
	</head>
	<body>
		<form action="logout.php">
		    <input class="btn btn-danger" type="submit" value="LogOut" />
		</form>
		<br>
		<table class = "table">
			<thead>
				<tr>
					<th scope="col">#</th>
					<th scope="col">username</th>
					<th scope="col">descrição</th>
					<th scope="col">inicio</th>
					<th scope="col">fim</th>
					<th scope="col">tempo total</th>
				</tr>
			</thead>
		<?php
			//select worklog data from this user
			$sql = "SELECT id,id_user, description, start, finish FROM worklog ORDER BY id_user";
			//for each row of data in worklog table, write a row in the html table
			if($stmt = mysqli_prepare($link, $sql))
			{
				if(mysqli_stmt_execute($stmt))
				{
					mysqli_stmt_store_result($stmt);
					if(mysqli_stmt_num_rows($stmt) >= 1)
					{
						$i=0;//checker to see if number of current itteration == last table row
						mysqli_stmt_bind_result($stmt, $id, $id_user, $description, $start, $finish);
						while (mysqli_stmt_fetch($stmt))
						{
							$i++;
							echo "<tr>";
							echo "<th scope='row'>".$id."</th>";

							//query to get the username from the FK in the worklog
							$sql = "SELECT username FROM users where id = ?";
							if($stmtName = mysqli_prepare($link, $sql))
							{
								mysqli_stmt_bind_param($stmtName, "i", $id_user);
								if(mysqli_stmt_execute($stmtName))
								{
									mysqli_stmt_store_result($stmtName);
									mysqli_stmt_bind_result($stmtName, $username);
									while (mysqli_stmt_fetch($stmtName))
										echo "<td scope='row'>".$username."</td>";
								}
							}

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
		<h2 style="text-align:right; margin-right:2%; margin-bottom:2%;">Total work: <?php echo $totalMinutes." m (".round($totalMinutes/60)." h)";?></h2>
	</body>
</html>
<?php
?>

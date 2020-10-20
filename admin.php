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
		<title>Admin</title>
		<style type="text/css">
			table {margin-bottom: 5% !important;}
	        body{color: #f0f0f0 !important; background-color: #2a2a2a !important; font: 18px sans-serif; position: relative; padding: 5%; }
	        .wrapper{ width: 350px; padding: 20px; }
	    </style>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	</head>
	<body>
		<form action="logout.php">
		    <input class="btn btn-danger" type="submit" value="LogOut" />
		</form>
		<br>
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

					}
				}
			}
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
						$i=0;//number of total rows
						$lastRowId_user=-1;
						$endTable=0;
						mysqli_stmt_bind_result($stmt, $id, $id_user, $description, $start, $finish);
						while (mysqli_stmt_fetch($stmt))
						{
							$i++;
							//if last row id_user is different from this row's id_user then start a new table
							if($lastRowId_user!=$id_user)
							{
								//at the beggining of each row (except the first), close the table and print total work
								if($i!=1)
								{
									?>
											<tr>
										      <td colspan="5"></td>
										      <td><h3><?php echo $totalMinutes." m (".round($totalMinutes/60)." h)";?></h3></td>
										    </tr>
											</table>
										</div>
									<?php
								}
								$totalMinutes=0;
								?>
								<div class="table-responsive">
									<table class = "table table-striped table-dark table-hover">
										<thead>
											<tr>
												<th scope="col" style="width: 3%">#</th>
												<th scope="col" style="width: 8%">username</th>
												<th scope="col" style="width: 54%">descrição</th>
												<th scope="col"style="width: 12.5%">inicio</th>
												<th scope="col"style="width: 12.5%">fim</th>
												<th scope="col"style="width: 10%">tempo total</th>
											</tr>
										</thead>
								<?php
							}
							$lastRowId_user=$id_user;

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
							if($endTable==1)
							{

							}
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
		<!-- close last table and print total work!-->
			<tr>
		      <td colspan="5"></td>
		      <td><h3><?php echo $totalMinutes." m (".round($totalMinutes/60)." h)";?></h3></td>
		    </tr>
			</table>
		</div>
	</body>
</html>
<?php
?>

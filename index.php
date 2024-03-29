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
		<style type="text/css">
			table {margin-top: 2% !important;}
	        body{color: #f0f0f0 !important; background-color: #2a2a2a !important; font: 18px sans-serif;!important position: relative; padding: 5%; }
	        .wrapper{ width: 350px; padding: 20px; }
	    </style>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
		<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
		<link rel="icon" type="image/png" href="media\favicon.png?">
		<link rel="shortcut icon" type="image/png" href="media\favicon.png?">
	</head>
	<body>

		<form action="logout.php">
		    <input class="btn btn-danger" type="submit" value="LogOut" />
		</form>
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
			<div class="table-responsive">
				<table class = "table table-striped table-dark table-hover">
					<thead>
						<tr>
							<th scope="col" style="width: 3%">#</th>
							<th scope="col" style="width: 54%">descrição</th>
							<th scope="col"style="width: 12.5%">inicio</th>
							<th scope="col"style="width: 12.5%">fim</th>
							<th scope="col"style="width: 18%">tempo total</th>
							<th scope="col"style="width: 3%">pago</th>
						</tr>
					</thead>
		<?php
			//select worklog data from this user
			$sql = "SELECT id, description, start, finish, paid FROM worklog WHERE id_user = ?";
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
						mysqli_stmt_bind_result($stmt, $id,$description, $start, $finish, $paid);
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

							if($paid == 0)
							{
								$totalMinutes=$totalMinutes + floor($minutes);
								$restMinutes=$totalMinutes - floor($totalMinutes/60)*60;
							}


							echo "<td>".floor($minutes)." m</td>";
							if ($paid == 0)
								echo "<td><i class='fa fa-close' style='font-size:24px;color:red'></td>";
							else
								echo "<td><i class='fa fa-check' style='font-size:24px;color:green'></i></td>";
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
			<tr>
				<td colspan="4"></td>
				<td colspan="2"><?php echo floor($totalMinutes/60)." h e ". $restMinutes." m por pagar";?>
					<hr>
					<span>
						<a href="http://meusalario.pt/salario/salariominimo">9310€ de salário mínimo anual na madeira de 2020</a> /
						<a href="https://www.dias-uteis.pt/dias-uteis_feriados_2020.htm">253 dias úteis</a>
						/ 8 horas diárias * <?php echo floor($totalMinutes/60);?> horas = <?php echo round(9310/253/8*floor($totalMinutes/60));?>€
					</span>
					<hr>
					<button style="position: relative; right: -80%;"type="button" class="btn btn-success pay">Pay</button>
				</td>
		    </tr>
			</table>
		</div>
		</form>
	</body>
	<script>
		$('.pay').click(function() {
		  $.ajax({
		    type: "POST",
		    url: "paid.php"
		  }).done(function() {
			location.reload();
		    alert("Hours paid");
		  });
		});
	</script>
</html>
<?php
?>

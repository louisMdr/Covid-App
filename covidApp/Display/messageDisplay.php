<?php require_once '../database.php';

if(!isset($_SESSION['user_id']) || $_SESSION['type'] == "Person")
{
	header('Location: ../login.php');
}
?>



<html>
    <head>
        <title>C19PHCS - Message Info</title>
		<link rel="stylesheet" href="../style.css">
	</head>

<?php include('../Employee/dropdownmenu.html');?>

<form action="messageDisplay.php" method="POST">
		<br><br>
	<div class="formDiv">
		<div>
			<label for="startDate"><h3 class="instruction">Please enter the Start Date:</h3><br></label>
			<input type="date" name="startDate" required>
		</div>
		<br>
		<div>
			<label for="endDate"><h3 class="instruction">Please enter the End Date:</h3><br></label>
			<input type="date" name="endDate" required>
		</div>
		<br>
	</div>
		<br>
		<!-- this goes to the up php code -->
		<input type="submit" name="search_msg" value="Search" />
	</form>


</body>
</html>






<?php


	if(isset($_POST['search_msg']))
	{


		$msgs = $db->prepare('SELECT * FROM comp353.messages WHERE ((Date >= :startDate) AND (Date <= :endDate))');
		$msgs->bindParam(':startDate', $_POST['startDate']);
		$msgs->bindParam(':endDate', $_POST['endDate']);
		$msgs->execute();
		$results = $msgs->fetchAll(PDO::FETCH_ASSOC);
		if(is_array($results) && count($results) > 0)
		{
			echo "<h2>All Messages:</h2><br>
		<table border='1'>
		<thead>
		    <tr>";
			
			$cols = $db->prepare('DESCRIBE comp353.messages');
			$cols->execute();
			$table_fields = $cols->fetchAll(PDO::FETCH_COLUMN);
			foreach ($table_fields as $value)
			{
				echo "<th>$value</th>";
			}

			echo "</tr></thead><tbody>";

			foreach($results as $places)
			{
			echo "<tr>";
				foreach ($places as $place)
				{
					//if its empty
					if($place == '')
					{
						echo "<td>None</td>";
					}
					else
					{
						echo "<td>'$place'</td>";
					}
				}
			echo "</tr>";	
			}

		echo "</tbody>
		</table>";
		}
		else
		{
			echo "<h3>No messages were found.</h3>";
		}


	}
?>
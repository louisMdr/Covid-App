<?php require_once '../database.php';

if(!isset($_SESSION['user_id']))
{
	header('Location: ../login.php');
}
?>

<html>
<head>
	<title>C19PHCS - Facility Info</title>
	<link rel="stylesheet" href="../style.css">
</head>

<?php 
if($_SESSION['type'] == "Person")
{
	include('../Person/dropdownmenu.html');
}
else
{
	include('../Employee/dropdownmenu.html');
}

echo "<br>";?>

<h2>All facilities information:</h2><br>

<?php
	//TODO: Find out about parents thing!!
	$facilities = $db->prepare('SELECT * FROM comp353.publichealthcenter WHERE isDeleted = 1');
	$facilities->execute();
	$results = $facilities->fetchAll(PDO::FETCH_ASSOC);
	if(is_array($results) && count($results) > 0)
	{
		echo "
	<table border='1'>
	<thead>
	    <tr>";

	    $cols = $db->prepare('DESCRIBE comp353.publichealthcenter');
		$cols->execute();
		$table_fields = $cols->fetchAll(PDO::FETCH_COLUMN);
		array_pop($table_fields);
		foreach ($table_fields as $value)
		{
			echo "<th>$value</th>";
		}

		echo "<th>Nbr of Workers</th>";

		echo "</tr></thead><tbody>";

	    $nbrWorkers = $db->prepare('SELECT COUNT(*) FROM comp353.publichealthworker WHERE Facility_Name = :FacilityName');
		foreach($results as $places)
		{
		array_pop($places);
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
		$nbrWorkers->bindParam(':FacilityName', $places['Facility_Name']);
		$nbrWorkers->execute();
		$total = $nbrWorkers->fetch(PDO::FETCH_ASSOC);

		echo "<td>" . $total['COUNT(*)'] . "</td>";
		echo "</tr>";	
		}
	}
	echo "</tbody>
	</table>";
	?>

</body>
</html>
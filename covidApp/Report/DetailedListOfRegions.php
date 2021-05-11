<?php require_once '../database.php';

if (!isset($_SESSION['user_id'])) {
	header('Location: ../login.php');
}
?>

<html>

<head>
	<title>C19PHCS - List Of Regions</title>
	<link rel="stylesheet" href="../style.css">
</head>

<?php
if ($_SESSION['type'] == "Person") {
	include('../Person/dropdownmenu.html');
} else {
	include('../Employee/dropdownmenu.html');
}

echo "<br>"; ?>

<h2>List of Regions Report</h2><br>

<?php

$facilities = $db->prepare(
	'SELECT City.City_Name, Region_Name, Post_Code
	 FROM comp353.City, comp353.postalcode, comp353.region
	 WHERE PostalCode.City_Name = City.City_Name AND 
		  City.Region_ID = Region.Region_ID;'
);
$facilities->execute();
$results = $facilities->fetchAll(PDO::FETCH_ASSOC);
if (is_array($results) && count($results) > 0) {
	echo "
	<table border='1'>
	<thead>
	<tr>";
	echo "<th>City_Name </th>";
	echo "<th>Region_Name</th>";
	echo "<th>Post_Code</th>";

	echo "</tr></thead><tbody>";
	foreach ($results as $places) {

		echo "<tr>";
		foreach ($places as $place) {
			//if its empty
			if ($place == '') {
				echo "<td>None</td>";
			} else {
				echo "<td>'$place'</td>";
			}
		}
	}
}
echo "</tbody>
	</table>";
?>

</body>

</html>
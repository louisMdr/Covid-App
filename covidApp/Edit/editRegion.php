<?php require_once '../database.php';

if(!isset($_SESSION['user_id']) || $_SESSION['type'] == "Person")
{
	header('Location: ../login.php');
}
?>

<html>
    <head>
        <title>C19PHCS - Edit Region</title>
		<link rel="stylesheet" href="../style.css">
</head>

<?php include('../Employee/dropdownmenu.html');?>

<form action="editRegion.php" method="POST">
		<br><br>
		<div class="formDiv">
			<label for="address"><h3 class="instruction">Please enter the Region's ID:</h3><br></label>
			<input type="text" name="region_id" required <?php echo isset($_POST['region_id']) ? 'value="' . $_POST['region_id'] . '"'  : 'value=""'; ?>>
		</div>
		<br>
		<!-- this goes to the up php code -->

	<?php
	//moved here for the first condition
	$cols = $db->prepare('DESCRIBE comp353.region');
	$cols->execute();
	$table_fields = $cols->fetchAll(PDO::FETCH_COLUMN);
	array_pop($table_fields);
	//to check if I have filled the full form (not just medical nbr)
	if(isset($_POST['searchEdit_Region']) && count($_POST) > 2)
	{
		$insertStr = 'UPDATE comp353.region SET ';
		//-1 because no isDeleted column
		for ($i=0; $i < count($table_fields); $i++) 
		{ 
			$index = "attrib" . $i;
			$insertStr = $insertStr . $table_fields[$i] . " = '" . $_POST[$index] . "', ";
		}
		//to remove the last comma
		$insertStr = substr($insertStr, 0, -2);
		$insertStr = $insertStr . " WHERE Region_ID = :region_id";
		$addPsn = $db->prepare($insertStr);
		$addPsn->bindParam(':region_id', $_POST['region_id']);
		$outcome = $addPsn->execute();
		// echo $insertStr;
		// echo $_POST['region_id'];
		if($outcome)
		{
			echo "<p>Change successful.</p>";
		}
		else
		{
			echo "<p>Error: Something went wrong. Please try again.</p>";
		}
		
		
	}
	//to check for medical record submit
	else if(!empty($_POST['region_id']) && isset($_POST['searchEdit_Region']))
	{
		//Not * so order is specific
		$person = $db->prepare('SELECT * FROM comp353.region WHERE Region_ID = :region_id AND isDeleted = 1');
		$person->bindParam(':region_id', $_POST['region_id']);
		$person->execute();
		//to get index type
		$results = $person->fetch(PDO::FETCH_BOTH);
		if(is_array($results) && count($results) > 0)
		{
			echo "<br><h3 class=\"edit\">" . $results['Region_Name'] . "'s Current Information:</h3><br>
		<table border='1'>
	<thead>
	    <tr>";

	    //previous place for table_fields
		foreach ($table_fields as $value)
		{
			echo "<th>$value</th>";
		}
		echo "</tr></thead><tbody>";

		echo "<tr>";

		array_pop($results);
		array_pop($results);
		
		//divide 2 since its both index and attribute name
		for($i=0; $i < count($results)/2; $i++)
		{
		//adds a row for said person & no check if its empty
		echo "<td>'$results[$i]'</td>";
		}

		echo "</tr></tbody></table>";
		
		echo "<br><br>";
		echo "<div class= \"formDiv\">
				<h3 class=\"instruction\">Please enter edits:</h3><br>";

		//remove this if changing the key is allowed.
		for ($i=0; $i <count($table_fields); $i++) 
		{ 
			echo 
			"<div>
			<br>
			<label for=\"attrib" . $i . "\">" . $table_fields[$i] . ":<br></label>
			<input type=\"text\" name=\"attrib" . $i . "\" required value=\"" . $results[$i];

			if($results[$i] != $_POST['region_id'])
			{
			 echo "\" ></div>";
			}
			else
			{
				echo "\" readonly></div>";
			}

		}

		echo "</div>";
		echo "<br>";

		}
		else
		{
			echo "<h3 class=\"instruction\">None with the Region ID: \"" . $_POST['region_id'] . "\" were found.</h3>";
		}
	}	
	?>

		<input type="submit" name="searchEdit_Region" value="Edit" />
	</form>
</body>
</html>


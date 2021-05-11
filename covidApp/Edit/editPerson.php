<?php require_once '../database.php';

if(!isset($_SESSION['user_id']) || $_SESSION['type'] == "Person")
{
	header('Location: ../login.php');
}
?>

<html>
    <head>
        <title>C19PHCS - Edit Person</title>
		<link rel="stylesheet" href="../style.css">
	</head>
<?php include('../Employee/dropdownmenu.html');?>

<form action="editPerson.php" method="POST">
		<br><br>
		<div class="formDiv">
			<label for="address"><h3 class="instruction">Please enter the Person's Medical Number:</h3><br></label>
			<input type="text" name="med_nbr" required <?php echo isset($_POST['med_nbr']) ? 'value="' . $_POST['med_nbr'] . '"'  : 'value=""'; ?>>
		</div>
		<br>
		<!-- this goes to the up php code -->

	<?php
	//moved here for the first condition
	$cols = $db->prepare('DESCRIBE comp353.person');
	$cols->execute();
	$table_fields = $cols->fetchAll(PDO::FETCH_COLUMN);
	array_pop($table_fields);
	//to check if I have filled the full form (not just medical nbr)
	if(isset($_POST['searchEdit_person']) && count($_POST) > 2)
	{
		$insertStr = 'UPDATE comp353.person SET ';
		//-1 because no isDeleted column
		for ($i=0; $i < count($table_fields); $i++)
		{ 
			$index = "attrib" . $i;
			$insertStr = $insertStr . $table_fields[$i] . " = '" . $_POST[$index] . "', ";
		}
		//to remove the last comma
		$insertStr = substr($insertStr, 0, -2);
		$insertStr = $insertStr . " WHERE Med_Num = :Med_Num";
		$addPsn = $db->prepare($insertStr);
		$addPsn->bindParam(':Med_Num', $_POST['med_nbr']);
		$outcome = $addPsn->execute();
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
	else if(!empty($_POST['med_nbr']) && isset($_POST['searchEdit_person']))
	{
		//Not * so order is specific
		$person = $db->prepare('SELECT * FROM comp353.person WHERE Med_Num = :Med_Num  AND isDeleted = 1');
		$person->bindParam(':Med_Num', $_POST['med_nbr']);
		$person->execute();
		//to get index type
		$results = $person->fetch(PDO::FETCH_BOTH);
		if(is_array($results) && count($results) > 0)
		{
			echo "<br><h3 class=\"edit\">" . $results['First_Name'] . " " . $results['Last_Name'] . "'s Current Information:</h3><br>
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
			<input type=\"text\" name=\"attrib" . $i;


			//parent med_nbr aren't obligatory
			if($i == count($table_fields)-1 || $i == count($table_fields)-2)
			{
				echo "\" value=\"" . $results[$i];
			}
			else
			{
				echo "\" required value=\"" . $results[$i];
			}

			if($results[$i] != $_POST['med_nbr'])
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
			echo "<h3 class=\"instruction\">None with the medical number: \"" . $_POST['med_nbr'] . "\" were found.</h3>";
		}
	}	
	?>

		<input type="submit" name="searchEdit_person" value="Edit" />
	</form>
</body>
</html>


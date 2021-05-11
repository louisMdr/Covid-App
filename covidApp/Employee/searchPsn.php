<?php require_once '../database.php';

if(!isset($_SESSION['user_id']) || $_SESSION['type'] == "Person")
{
	header('Location: ../login.php');
}
?>

<html>
    <head>
        <title>C19PHCS - Living With Search</title>
		<link rel="stylesheet" href="../style.css">
	</head>

<?php include('dropdownmenu.html');?>

<form action="searchPsn.php" method="POST">
		<br><br>
		<div class="formDiv">
			<label for="address"><h3 class="instruction">Please enter the Person's address:</h3><br></label>
			<input type="text" name="address" required>
		</div>
		<br>
		<!-- this goes to the up php code -->
		<input type="submit" name="search_adr" value="Search" />
		<br>
	</form>

	<?php
	if(!empty($_POST['address']))
	{
		//Not * so order is specific
		$sicks = $db->prepare('SELECT First_Name, Last_Name, Date_Birth, Med_Num, Tel_Num, Citizenship, Email FROM comp353.person WHERE Address = :Address');
		$sicks->bindParam(':Address', $_POST['address']);
		$sicks->execute();
		$results = $sicks->fetchAll(PDO::FETCH_ASSOC);
		if(is_array($results) && count($results) > 0)
		{
			echo "
		<table border='1'>
		<thead>
		    <tr>
		        <th>First Name</th>
		        <th>Last Name</th>
		        <th>Date of Birth</th>
		        <th>Medical Number</th>
		        <th>Telephone Number</th>
		        <th>Citizenship</th>
		        <th>Email</th>
		        <th>Father Name</th>
		        <th>Mother Name</th>
		    </tr>
		</thead>
		<tbody>";
			foreach($results as $person)
			{
			//adds a row per person
			echo "<tr>";
				foreach ($person as $value)
				{
					//if its empty
					if($value == '')
					{
						echo "<td>None</td>";
					}
					else
					{
						echo "<td>'$value'</td>";
					}
				}
			//handles parents names !! has gender column to Person & sorts from male to female
			$parent1 = $db->prepare('SELECT First_Name, Last_Name FROM person WHERE Med_Num = (SELECT Parent_1 FROM person WHERE Med_Num = :med_nbr)');
			$parent1->bindParam(':med_nbr', $person['Med_Num']);
			$parent1->execute();
			$p1Result = $parent1->fetch(PDO::FETCH_ASSOC);

			$parent2 = $db->prepare('SELECT First_Name, Last_Name FROM person WHERE Med_Num = (SELECT Parent_2 FROM person WHERE Med_Num = :med_nbr)');
			$parent2->bindParam(':med_nbr', $person['Med_Num']);
			$parent2->execute();
			$p2Result = $parent2->fetch(PDO::FETCH_ASSOC);
			
			echo "<td>" . $p1Result["First_Name"] . " " . $p1Result["Last_Name"] . "</td>";
			echo "<td>" . $p2Result["First_Name"] . " " . $p2Result["Last_Name"] . "</td>";
			
			echo "</tr>";	
			}
		}
		else
		{
			echo "<h3>None with the address: \"" . $_POST['address'] . "\" were found.</h3>";
		}
	}
	echo "</tbody>
	</table>";
	?>


<?php require_once '../database.php';

if(!isset($_SESSION['user_id']) || $_SESSION['type'] == "Person")
{
	header('Location: ../login.php');
}

?>

<html>
    <head>
        <title>C19PHCS - Symptom Search</title>

<?php include('../Employee/dropdownmenu.html');?>

<form action="searchSympt.php" method="POST">
		<br><br>
		<div class="formDiv">
			<label for="med_nbr"><h3 class="instruction">Please enter the Person's Medical Number:</h3><br></label>
			<input type="text" name="med_nbr" required <?php echo isset($_POST['med_nbr']) ? 'value="' . $_POST['med_nbr'] . '"'  : 'value=""'; ?>>
		</div>
	</div>
		<br>
		<input type="submit" name="search_adr" value="Search" />
		<br>
		<!-- this goes to the up php code -->

	<?php

	if(isset($_POST['search_adr']))
	{
		$fullTable = $db->prepare('SELECT personhas.PositiveCase_ID, personhas.FollowUp_Date, symptoms.Description 
								   FROM comp353.personhas INNER JOIN comp353.symptoms ON personhas.Symptom_ID = symptoms.Symptom_ID 
								   ORDER BY personhas.FollowUp_Date DESC');
		$fullTable->execute();
		$tableResult = $fullTable->fetchAll(PDO::FETCH_ASSOC);

		$posID = $db->prepare('SELECT PositiveCase_ID 
							   FROM comp353.positivecase 
							   WHERE Med_Num = :Med_Num');
		$posID->bindParam(':Med_Num', $_POST['med_nbr']);
		$posID->execute();
		$posCaseID = $posID->fetchAll(PDO::FETCH_ASSOC);

		echo "<div class=\"formDiv\"><h2>Symptoms Progress Report</h2><br>
		<table border='1'>
		<thead>
		    <tr>
		    <th>FollowUp_Date</th>
		    <th>Description</th>
		    </tr></thead><tbody>";

			foreach($tableResult as $row)
			{
				$sameID = false;

				foreach ($posCaseID as $value)
				{
					if($value['PositiveCase_ID'] == $row['PositiveCase_ID'])
					{
						$sameID = true;
						break;
					}
				}

				if($sameID)
				{
					array_shift($row);
					echo "<tr>";
						foreach ($row as $value)
						{
							//if its empty
							if($value == "")
							{
								echo "<td>None</td>";
							}
							else
							{
								echo "<td>'$value'</td>";
							}
						}
					echo "</tr>";
				}
				else
				{
					continue;
				}				
			}

		echo "</tbody>
		</table></div>";


	}

	?>
	</form>
</body>
</html>


<?php require_once '../database.php';

if (!isset($_SESSION['user_id'])) {
	header('Location: ../login.php');
}
?>

<html>

<head>
	<title>C19PHCS - Region Cases</title>
	<link rel="stylesheet" href="../style.css">
</head>

<?php
if ($_SESSION['type'] == "Person") {
	include('../Person/dropdownmenu.html');
} else {
	include('../Employee/dropdownmenu.html');
}

echo "<br>"; ?>



<?php
//TODO: Find out about parents thing!!

echo "<h2> Positive cases by region </h2>";

echo "<br>";
echo "<br>";


$facilities = $db->prepare(
	'SELECT  distinct   region.Region_Name,positivecase.Med_Num
		
	FROM person,positivecase,city,postalcode,region
	
	where  region.Region_ID = city.Region_ID
	and postalcode.City_Name= city.city_name
and postalcode.Post_Code = Person.Post_Code
and postalcode.Post_Code = Person.Post_Code
and Person.Med_Num = positivecase.Med_Num;

    '
);
$facilities->execute();
$results = $facilities->fetchAll(PDO::FETCH_ASSOC);

if (is_array($results) && count($results) > 0) {
	echo "
	<table border='1'>
	<thead>
	    <tr>";

/*
		SELECT distinct   region.Region_Name,positivecase.Med_Num
FROM person,positivecase,city,postalcode,region
where  region.Region_ID = city.Region_ID
and postalcode.City_Name= city.city_name
 and postalcode.Post_Code = Person.Post_Code
and Person.Med_Num = positivecase.Med_Num;
*/

	$cols = $db->prepare(
		'SELECT  distinct   region.Region_Name,positivecase.Med_Num
		
		FROM person,positivecase,city,postalcode,region
		
		where  region.Region_ID = city.Region_ID
		and postalcode.City_Name= city.city_name
	and postalcode.Post_Code = Person.Post_Code
	and postalcode.Post_Code = Person.Post_Code
	and Person.Med_Num = positivecase.Med_Num;
		'
	);
	$cols->execute();
	$table_fields = $cols->fetchAll(PDO::FETCH_COLUMN);
	echo "<th>Region_Name</th>";
	echo "<th> Med_Num</th>";

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

	echo "</tbody>
	</table>";

	echo "<br>";
	$var = count($results);
	echo "<h3> The total number of positive cases is  $var </h3> ";


	echo "<br>";

	$facilities = $db->prepare(
		'SELECT  Region_Name  from comp353.Region
    '
	);
	$facilities->execute();
	$results = $facilities->fetchAll(PDO::FETCH_COLUMN);

	/*
     foreach($results as $result){
          echo $result;
          echo "<br>";
     }
*/
	$cols2 = $db->prepare(
		'SELECT  distinct   region.Region_Name,positivecase.Med_Num
		
		FROM person,positivecase,city,postalcode,region
		
		where  region.Region_ID = city.Region_ID
		and postalcode.City_Name= city.city_name
	and postalcode.Post_Code = Person.Post_Code
	and postalcode.Post_Code = Person.Post_Code
	and Person.Med_Num = positivecase.Med_Num;
    '
	);
	$cols2->execute();
	$array6 = $cols2->fetchAll(PDO::FETCH_COLUMN);

	echo "<br>";
	echo "<br>";

	/*
       foreach($array6 as $array){
          echo $array;
          echo "<br>";
     }*/

	$Displayarray = array();

	$count1 = 0;
	$count2 = 0;
	$verif3 = "false";
	foreach ($results as $result) {
		$count2 = 1;
		$verif3 = "false";
		foreach ($array6 as $array) {
			if ($array == $result) {
				// echo "<br>";
				//echo $result;
				// echo "<br>";
				//echo $count2;
				$verif3 = "true";
				$Displayarray[$count1] = $count2;
				$count2++;
			}
		}
		if ($count2 = count($array6) &&  $verif3 == "false")
			$Displayarray[$count1] = "0";
		$count1++;
	}



	$facilities = $db->prepare(
		'SELECT distinct  Region_Name

from comp353.Region
    
    '
	);
	$facilities->execute();
	$results4 = $facilities->fetchAll(PDO::FETCH_ASSOC);
	if (is_array($results4) && count($results4) > 0) {
		echo "
	<table border='1'>
	<thead>
	    <tr>";
		$count2 = 0;

		$table_fields = $cols->fetchAll(PDO::FETCH_COLUMN);
		echo "<th>Region_Name</th>";
		echo "<th> Number of Cases </th>";

		echo "</tr></thead><tbody>";
		$count2 = 0;
		foreach ($results4 as $places2) {

			echo "<tr>";
			foreach ($places2 as $place) {
				if ($place == '') {
					echo "<td>None</td>";
				} else {
					echo "<td>'$place' </td>";
				}
			}
			$value = $Displayarray[$count2];
			echo "<td>$value  </td>";
			$count2++;
		}
	}
	echo "</tbody>
	</table>";



	echo "<br>";
	echo "<br>";
	echo "<br>";
	/**
	 * -----------------------------------------------------------------------------------------------------------------
	 */


	echo "<h2> Negative cases by region </h2>";

	echo "<br>";
	echo "<br>";

	$facilities = $db->prepare(
		'SELECT distinct Region_Name, diagnostic.Med_Num
    
    FROM Region,Person,Diagnostic,city,postalcode
    
where Region.Region_ID = city.Region_ID

and postalcode.City_Name = city.City_Name

AND Person.Post_Code = postalcode.Post_Code

and person.Med_Num = Diagnostic.Med_Num
AND diagnostic.PCR_Result =:Negative
    '
	);

	$negative = 'Negative';
	$facilities->bindParam(':Negative', $negative);
	$facilities->execute();
	$results = $facilities->fetchAll(PDO::FETCH_ASSOC);

	if (is_array($results) && count($results) > 0) {
		echo "
	<table border='1'>
	<thead>
	    <tr>";

		$cols = $db->prepare(
			'SELECT distinct Region_Name, diagnostic.Med_Num
    
			FROM Region,Person,Diagnostic,city,postalcode
			
		where Region.Region_ID = city.Region_ID
		
		and postalcode.City_Name = city.City_Name
		
		AND Person.Post_Code = postalcode.Post_Code
		
		and person.Med_Num = Diagnostic.Med_Num
		AND diagnostic.PCR_Result =:Negative
			'
		);

		$cols->bindParam(':Negative', $negative);
		$cols->execute();
		$table_fields = $cols->fetchAll(PDO::FETCH_COLUMN);
		echo "<th>Region_Name</th>";
		echo "<th> Med_Num</th>";

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

		echo "</tbody>
	</table>";

		echo "<br>";
		$var =  count($results);

		echo "<h3> The total number of Negative cases is $var </h3> ";

		echo "<br>";

		$facilities = $db->prepare(
			'SELECT  Region_Name  from comp353.Region
    '
		);
		$facilities->execute();
		$results = $facilities->fetchAll(PDO::FETCH_COLUMN);

		/*
     foreach($results as $result){
          //echo $result;
          echo "<br>";
     }
*/
		$cols2 = $db->prepare(
			'SELECT distinct Region_Name, diagnostic.Med_Num
    
			FROM Region,Person,Diagnostic,city,postalcode
			
		where Region.Region_ID = city.Region_ID
		
		and postalcode.City_Name = city.City_Name
		
		AND Person.Post_Code = postalcode.Post_Code
		
		and person.Med_Num = Diagnostic.Med_Num
		AND diagnostic.PCR_Result =:Negative
			'
		);
		$cols2->bindParam(':Negative', $negative);
		$cols2->execute();
		$array6 = $cols2->fetchAll(PDO::FETCH_COLUMN);

		echo "<br>";
		echo "<br>";

		/*
       foreach($array6 as $array){
          echo $array;
          echo "<br>";
     }*/

		$Displayarray = array();

		$count1 = 0;
		$count2 = 0;
		$verif3 = "false";
		foreach ($results as $result) {
			$count2 = 1;
			$verif3 = "false";
			foreach ($array6 as $array) {
				if ($array == $result) {
					//echo "<br>";
					//  echo $result;
					// echo "<br>";
					// echo $count2;
					$verif3 = "true";
					$Displayarray[$count1] = $count2;
					$count2++;
				}
			}
			if ($count2 = count($array6) &&  $verif3 == "false")
				$Displayarray[$count1] = "0";
			$count1++;
		}



		$facilities = $db->prepare(
			'SELECT distinct  Region_Name

from comp353.Region
    
    '
		);
		$facilities->execute();
		$results4 = $facilities->fetchAll(PDO::FETCH_ASSOC);
		if (is_array($results4) && count($results4) > 0) {
			echo "
	<table border='1'>
	<thead>
	    <tr>";
			$count2 = 0;

			$table_fields = $cols->fetchAll(PDO::FETCH_COLUMN);
			echo "<th>Region_Name</th>";
			echo "<th> Number of Cases </th>";

			echo "</tr></thead><tbody>";
			$count2 = 0;
			foreach ($results4 as $places2) {

				echo "<tr>";
				foreach ($places2 as $place) {
					if ($place == '') {
						echo "<td>None</td>";
					} else {
						echo "<td>'$place' </td>";
					}
				}
				$value = $Displayarray[$count2];
				echo "<td>$value  </td>";
				$count2++;
			}
		}
		echo "</tbody>
	</table>";
	}


	/**
	 * 
	 * Now alert in a specefic period of time
	 * 
	 */


	if (isset($_POST['Date1']) && isset($_POST['Date2'])) {


		$Date1 = $_POST['Date1'];
		$Date2 = $_POST['Date2'];

		echo "<h3> These are going to be the results in a specific period , all the results after $Date1  and before $Date2 </h3> ";
		echo "<br>";

		$facilities = $db->prepare(
			'SELECT  Region_Name,Alert_Date,RegionHas.Level 
    
    FROM  RegionHas , alert
    
    where RegionHas.Level = alert.Level 

  and Alert_Date > :Date1 and Alert_Date < :Date2   ;
    '
		);
		$facilities->bindParam(':Date1', $Date1);
		$facilities->bindParam(':Date2', $Date2);
		$facilities->execute();
		$results = $facilities->fetchAll(PDO::FETCH_ASSOC);
		if (is_array($results) && count($results) > 0) {
			echo "
	<table border='1'>
	<thead>
	    <tr>";

			$cols = $db->prepare(
				'SELECT  Region_Name,Alert_Date,RegionHas.Level 
    
    FROM  RegionHas , alert
    
    where RegionHas.Level = alert.Level 

  and Alert_Date > :Date1 and Alert_Date < :Date2   ;
    '
			);
			$cols->bindParam(":Date1", $Date1);
			$cols->bindParam(":Date2", $Date2);
			$cols->execute();
			$table_fields = $cols->fetchAll(PDO::FETCH_COLUMN);
			echo "<th>Region_Name, </th>";
			echo "<th>Alert_Date</th>";
			echo "<th>Level</th>";

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
			echo "</tbody>
	</table>";
		} else
			echo "<h3> There has been no result in the period between $Date1 and $Date2. </h3>";
	}
}

?>


<h3 class="instruction">Please enter two dates so we could display the restults for a specific period of Time</h3>

<div class="formDiv">
	<form action="RegionsCases.php" method="POST">

		<br>

		<input type="date" id="Date1" name="Date1" class="date">
		<br>

		<br>

		<input type="date" id="Date2" name="Date2" class="date">
		<br>

		<input type="submit" value="Submit">
	</form>
</div>

</body>

</html>
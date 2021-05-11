<?php require_once '../database.php';

if (!isset($_SESSION['user_id'])) {
  header('Location: ../login.php');
}
?>

<html>

<head>
  <title>C19PHCS - Workers Tested Positive</title>
  <link rel="stylesheet" href="../style.css">
</head>

<?php
if ($_SESSION['type'] == "Person") {
  include('../Person/dropdownmenu.html');
} else {
  include('../Employee/dropdownmenu.html');
}

echo "<br>"; ?>

<h2>Workers Tested Positive on Specific Date Report</h2><br>

<?php

$facilitiesx = $db->prepare('SELECT * FROM comp353.publichealthcenter');
$facilitiesx->execute();
$resultsx = $facilitiesx->fetchAll(PDO::FETCH_COLUMN);

$result = 'Positive';

$facilitiesy = $db->prepare('SELECT Date_Result FROM comp353.diagnostic
                             where PCR_Result = :Result');
$facilitiesy->bindParam(':Result', $result);
$facilitiesy->execute();
$resultsy = $facilitiesy->fetchAll(PDO::FETCH_COLUMN);


if (isset($_POST['Facility'])   && isset($_POST['date'])) {

  $Facility = $_POST['Facility'];
  $date = $_POST['date'];
  try {

    $facilities = $db->prepare(
      'SELECT First_Name,Last_Name,diagnostic.Med_Num,PCR_Result,publichealthworker.Facility_Name,Date_Tested
       FROM Person,publichealthworker,diagnostic
       WHERE Person.Med_Num = Diagnostic.Med_Num AND Person.Med_Num =publichealthworker.Med_Num
       AND publichealthworker.Med_Num = Diagnostic.Med_Num AND PCR_Result = :Positive 
       AND  diagnostic.Date_Result = :Dates AND publichealthworker.Facility_Name =:Facility;
    '
    );
    $facilities->bindParam(':Positive', $result);
    $facilities->bindParam(':Dates', $date);
    $facilities->bindParam(':Facility', $Facility);
    $facilities->execute();
    $results = $facilities->fetchAll(PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
    die('Could not connect to database: ' . $e->getMessage());
  }
  if (is_array($results) && count($results) > 0) {
    echo "
	  <table border='1'>
	  <thead>
	    <tr>";

    echo "<th>First_name </th>";
    echo "<th>Last_Name</th>";
    echo "<th>Med_Num</th>";
    echo "<th>PCR_Result</th>";
    echo "<th>Facility_Name</th>";
    echo "<th>Date_Tested</th>";

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
  } else {
    echo "<h3> There has been no tests performed on the $date at the facility $Facility </h3>";
    echo "<br>";
    echo "<br>";
    echo "<br>";
  }
  // ==============================> WORKERS THAT WORKED WITH POSITIVE CASE
  echo "<h3 class=\"instruction\"> The workers who worked with the positive individual(s) 14 days prior to PCR Result </h3>";
  echo "<br>";

  /**
   * 
   * nOw handling the fourteen days
   */

  $start_date = $date;
  $Date = DateTime::createFromFormat('Y-m-d', $start_date);
  $Date2 = DateTime::createFromFormat('Y-m-d', $start_date);

  $Date->modify('-15 day');

  $Date2->modify('+1 day');

  $newDate;

  $newDate = $Date->format('Y-m-d');
  $newDate2 = $Date2->format('Y-m-d');

  /*
  Now for the employees who have been working with this person for fourtneed days prior 
  */
  try {
    $facilities = $db->prepare(
      'SELECT First_Name,Last_Name,Schedule_ID, schedule.Emp_ID,Date 
       FROM publichealthworker  , schedule,person
       WHERE schedule.Emp_ID =publichealthworker.Emp_ID AND publichealthworker.Facility_Name = :Facility  AND schedule.Date> :NewDate 
       AND schedule.Date < :NewDate2 AND person.Med_Num = publichealthworker.Med_Num
      ');

    $facilities->bindParam(':Facility', $Facility);
    $facilities->bindParam(':NewDate', $newDate);
    $facilities->bindParam(':NewDate2', $newDate2);
    $facilities->execute();
    $results = $facilities->fetchAll(PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
    die('Could not connect to database: ' . $e->getMessage());
  }
  if (is_array($results) && count($results) > 0) {
    echo "
	  <table border='1'>
	  <thead>
	  <tr>";

    echo "<th>First_name </th>";
    echo "<th>Last_Name</th>";
    echo "<th>Schedule_ID</th>";
    echo "<th>Emp_ID</th>";
    echo "<th>Date</th>";

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
    echo "<h3 class=\"instruction\"> There has been no workers working on the  period from $newDate to $newDate2 at the facility $Facility </h3>";

  echo "<br>";
}
?>

<div class="formDiv">
  <h3 class="instruction">Please select a facility name and a specific date to display the workers who were tested positive on that date</h3>
  <br>
  <form action="WorkersTestedPositive.php" method="POST">
    <br>
    Facility
    <br>
    <select id="Facility" name="Facility">
      <?php foreach ($resultsx  as $result) {

      ?>
        <option value="<?php echo $result ?>"><?php echo $result ?></option>
      <?php } ?>
    </select>
    <br>

    <br>
    Dates of Positive Results in the System
    <br>
    <select id="date" name="date" class="date">
      <?php foreach ($resultsy  as $result) {

      ?>
        <option value="<?php echo $result ?>"><?php echo $result ?></option>
      <?php } ?>
    </select>
    <br>

    <br>
    <input type="submit" value="Submit">
  </form>
</div>

</body>

</html>
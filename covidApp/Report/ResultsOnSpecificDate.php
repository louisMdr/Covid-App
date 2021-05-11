<?php require_once '../database.php';

if (!isset($_SESSION['user_id'])) {
  header('Location: ../login.php');
}
?>

<html>

<head>
  <title>C19PHCS - Results Specific Date</title>
  <link rel="stylesheet" href="../style.css">
</head>

<?php
if ($_SESSION['type'] == "Person") {
  include('../Person/dropdownmenu.html');
} else {
  include('../Employee/dropdownmenu.html');
}

echo "<br>"; ?>

<h2>Results on Specific Date Report</h2><br>

<?php

if (isset($_POST['Date'])) {

  $date = $_POST['Date'];

  $facilities = $db->prepare(
    'SELECT PCR_Result,diagnostic.Med_Num,First_Name,Last_Name,Date_Result,email,Tel_Num,Date_Birth
     FROM comp353.person,comp353.diagnostic
     WHERE person.Med_num = diagnostic.Med_Num and Date_Result=:dates
     ORDER BY PCR_Result desc;
    '
  );
  $facilities->bindParam(':dates', $date);
  $facilities->execute();
  $results = $facilities->fetchAll(PDO::FETCH_ASSOC);
  if (is_array($results) && count($results) > 0) {
    echo "
	<table border='1'>
	<thead>
	    <tr>";

    echo "<th>Result </th>";
    echo "<th>Med_Num</th>";
    echo "<th>First_Name</th>";
    echo "<th>Last_Name </th>";
    echo "<th>Date_Result</th>";
    echo "<th>Email</th>";
    echo "<th>Tel_Num </th>";
    echo "<th>Date_Birth</th>";

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
    echo "<h3 class=\"instruction\"> There has been no test performed on the $date </h3>";
}
?>

<div class="formDiv">
  <h3 class="instruction">Please enter a date so we could display the results</h3>
  <br>
  <form action="ResultsOnSpecificDate.php" method="POST">

    <br>

    <input type="date" id="Date" name="Date" class="date">
    <br>

    <br>
    <input type="submit" value="Submit">
  </form>
</div>

</body>

</html>
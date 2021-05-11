<?php require_once '../database.php';


if (!isset($_SESSION['user_id'])) {
  header('Location: ../login.php');
}
?>

<html>

<head>
  <title>C19PHCS - Workers In Facilities</title>
  <link rel="stylesheet" href="../style.css">
</head>

<?php
if ($_SESSION['type'] == "Person") {
  include('../Person/dropdownmenu.html');
} else {
  include('../Employee/dropdownmenu.html');
}

echo "<br>"; ?>

<h2>Workers at a Facility Report</h2><br>

<?php

$facilitiesx = $db->prepare('SELECT * FROM comp353.publichealthcenter');
$facilitiesx->execute();
$resultsx = $facilitiesx->fetchAll(PDO::FETCH_COLUMN);

if (isset($_POST['Facility'])) {

  $Facility = $_POST['Facility'];

  try {

    $facilities = $db->prepare(
      'SELECT First_name,Last_Name,Facility_Name,publichealthworker.Med_Num
       FROM comp353.person , comp353.publichealthworker
       WHERE person.Med_Num = publichealthworker.Med_Num and Facility_Name = :FacilityName
    ');
    $facilities->bindParam(':FacilityName', $Facility);
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
    echo "<th>Last_Name,</th>";
    echo "<th>Facility_Name</th>";
    echo "<th>Med_Num </th>";

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
  }
}
?>

<div class="formDiv">
  <h3 class="instruction">Please choose a facility so we could display all the workers in that location</h3>
  <br>
  <form action="WorkersInFacility.php" method="POST">
    <br>
    <select id="Facility" name="Facility">
      <?php foreach ($resultsx  as $result) {

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
<?php require_once '../database.php';

if (!isset($_SESSION['user_id'])) {
  header('Location: ../login.php');
}
?>

<html>

<head>
  <title>C19PHCS - Delete PublicHealthWorker</title>
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

  /**
   * Populating the drop down menu for employeeID
   * 
   */
  $empIDQuery = $db->prepare('SELECT Emp_ID FROM comp353.publichealthworker');
  $empIDQuery->execute();
  $empIDs = $empIDQuery->fetchAll(PDO::FETCH_COLUMN);

  if (isset($_POST['Emp_ID'])) {

    $Emp_ID = $_POST['Emp_ID'];

    //=======================================> VERIFICATION OF EMPLOYEE ID
    /**
     * Verifying if the employee ID exists in the database
     */
    $_empIDQuery = $db->prepare('SELECT Emp_ID FROM comp353.publichealthworker');
    $_empIDQuery->execute();
    $resulty = $_empIDQuery->fetchAll(PDO::FETCH_COLUMN);

    $verif = false;

    $message = "";
    foreach ($resulty as $res) {

      if ($res == $Facility) {
        $verif = true;
      }
      else {
        $message = "The employee ID $Emp_ID, does not exist.";
      }
    }

    // if verif is true then medical number entry exists in database
    if ($verif == true) {
      try {

        $employeeQuery = $db->prepare('SELECT Emp_ID FROM comp353.publichealthworker');
        $employeeQuery->execute();
        $results = $employeeQuery->fetchAll(PDO::FETCH_COLUMN);
      } catch (PDOException $e) {
        die('Could not connect to database: ' . $e->getMessage());
      }

      // =====================================> SETTING ISDELETED FOR EMPLOYEE ID
      try {
        $isDeleted = 0;

        $colz = $db->prepare('UPDATE comp353.publichealthworker c
                              SET c.isDeleted = :isDeleted
                              WHERE c.Emp_ID = :Emp_ID;');

        $colz->bindParam(':Emp_ID', $Emp_ID);
        $colz->bindParam(':isDeleted', $isDeleted);
        $colz->execute();

        $table_fields = $colz->fetchAll(PDO::FETCH_COLUMN);
      } catch (PDOException $e) {
        die('Could not connect to database: ' . $e->getMessage());
      }
      // ====================

      echo "<br>";
      echo "<h3 class=\"instruction\"> The employee ID, $Emp_ID, has been succesfully removed. Please check the display page to verify.</h3>";

    } else
      echo "<h3 class=\"instruction\"> $message </h3>";

    echo "<br>";
    echo "<br>";
    echo "<br>";
  }
  ?>

  <div class= "formDiv">
    <h3 class="instruction">Please select the employee ID you would like to remove from database</h3>
    <br>
    <form action="facilityDelete.php" method="POST">

      <br>
      Employee ID
      <br>
      <select id="Emp_ID" name="Emp_ID">
        <?php foreach ($empIDs  as $result) {

        ?>
          <option value="<?php echo $result ?>"><?php echo $result ?></option>
        <?php } ?>
      </select>
      <br>

      <br>
      <input type="submit" value="Delete">
    </form>
  </div>

  </body>

</html>
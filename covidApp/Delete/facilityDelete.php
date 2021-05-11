<?php require_once '../database.php';

if (!isset($_SESSION['user_id'])) {
  header('Location: ../login.php');
}
?>

<html>

<head>
  <title>C19PHCS - Delete Facility</title>
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
   * Populating the drop down menu for facility.
   * 
   */
  $facilityQuery = $db->prepare('SELECT Facility_Name FROM comp353.publichealthcenter');
  $facilityQuery->execute();
  $facilities = $facilityQuery->fetchAll(PDO::FETCH_COLUMN);

  if (isset($_POST['Facility'])) {

    $Facility = $_POST['Facility'];

    //=======================================> VERIFICATION OF FACILITY NAME
    /**
     * Verifying if the facility name exists in the database
     */
    $facilityQuery = $db->prepare('SELECT Facility_Name FROM comp353.publichealthcenter');
    $facilityQuery->execute();
    $resulty = $facilityQuery->fetchAll(PDO::FETCH_COLUMN);

    $verif = false;

    $message = "";
    foreach ($resulty as $res) {

      if ($res == $Facility) {
        $verif = true;
      }
      else {
        $message = "The facility with this name, $Facility, does not exist.";
      }
    }

    // if verif is true then medical number entry exists in database
    if ($verif == true) {
      try {

        $_facilityQuery = $db->prepare('SELECT Facility_Name FROM comp353.publichealthcenter');
        $_facilityQuery->execute();
        $results = $_facilityQuery->fetchAll(PDO::FETCH_COLUMN);
      } catch (PDOException $e) {
        die('Could not connect to database: ' . $e->getMessage());
      }

      // =====================================> SETTING ISDELETED FOR FACILITY
      try {
        $isDeleted = 0;

        $colz = $db->prepare('UPDATE comp353.publichealthcenter c
                              SET c.isDeleted = :isDeleted
                              WHERE c.Facility_Name = :Facility_Name;');

        $colz->bindParam(':Facility_Name', $Facility);
        $colz->bindParam(':isDeleted', $isDeleted);
        $colz->execute();

        $table_fields = $colz->fetchAll(PDO::FETCH_COLUMN);
      } catch (PDOException $e) {
        die('Could not connect to database: ' . $e->getMessage());
      }
      // ====================

      echo "<br>";
      echo "<h3 class=\"instruction\"> The facility, $Facility, has been succesfully removed. Please check the display page to verify.</h3>";

    } else
      echo "<h3 class=\"instruction\"> $message </h3>";

    echo "<br>";
    echo "<br>";
    echo "<br>";
  }
  ?>

  <div class= "formDiv">
    <h3 class="instruction">Please select the name of the facility you would like to remove from database</h3>
    <br>
    <form action="facilityDelete.php" method="POST">

      <br>
      Facility Name
      <br>
      <select id="Facility" name="Facility">
        <?php foreach ($facilities  as $result) {

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
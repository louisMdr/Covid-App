<?php require_once '../database.php';

if (!isset($_SESSION['user_id'])) {
  header('Location: ../login.php');
}
?>

<html>

<head>
  <title>C19PHCS - Create Facility</title>
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
   * Verifying if the user has entered all the informations
   */
  if (
    isset($_POST['Facility'])   && isset($_POST['Adress'])  && isset($_POST['type'])  && isset($_POST['phone'])

    && isset($_POST['Web'])  && isset($_POST['Drive'])  && isset($_POST['Test'])

  ) {

    $FACILITY = $_POST['Facility'];
    $ADRESS = $_POST['Adress'];
    $TYPE = $_POST['type'];
    $PHONE = $_POST['phone'];
    $WEB = $_POST['Web'];
    $DRIVE = $_POST['Drive'];
    $TEST = $_POST['Test'];

    //=======================================> VERIFICATION OF FACILITY ENTRY
    /**
     * Verifying if the input is already a key in which case we can't allow this entry and also web adress and phone number in case of uniquness
     */
    $facilityQuery = $db->prepare('SELECT Facility_Name FROM comp353.publichealthcenter');
    $facilityQuery->execute();
    $facilities = $facilityQuery->fetchAll(PDO::FETCH_COLUMN);

    $verif = true;

    $message = "";
    foreach ($facilities  as $res) {

      if ($res == $FACILITY) {
        $verif = false;
        $message = "The facility, $FACILITY, cannot be added to the database because the entry already exists.";
      }
    }

    $phoneNumQuery = $db->prepare('SELECT Phone_Num FROM comp353.publichealthcenter');
    $phoneNumQuery->execute();
    $phoneNums = $phoneNumQuery->fetchAll(PDO::FETCH_COLUMN);

    $verif2 = true;

    foreach ($phoneNums  as $res) {

      if ($res == $PHONE) {
        $verif2 = false;
        $message = "The phone number, $PHONE, cannot be added to the database because the entry already exists.";
      }
    }

    $webAddrQuery = $db->prepare('SELECT Web_Addr FROM comp353.publichealthcenter');
    $webAddrQuery->execute();
    $webAddrs = $webAddrQuery->fetchAll(PDO::FETCH_COLUMN);

    $verif3 = true;

    foreach ($webAddrs  as $res) {

      if ($res == $WEB) {
        $verif3 = false;
        $message = "The web address, $WEB, cannot be added to the database because the entry already exists.";
      }
    }

    // if verification is valid then facility entry is not a duplicate 
    if ($verif == true &&  $verif2 == true && $verif3 == true) {
      try {

        $facilities = $db->prepare('SELECT Facility_Name  from comp353.publichealthcenter ');
        $facilities->execute();
        $results = $facilities->fetchAll(PDO::FETCH_COLUMN);
      } catch (PDOException $e) {
        die('Could not connect to database: ' . $e->getMessage());
      }

      // ====================> INSERTING NEW FACILITY ENTRY
      try {
        $isDeleted = 1;

        $colz = $db->prepare('INSERT Into comp353.publichealthcenter 
                              VALUES (:Facility,:Adress,:FacilityType,:Phone,:Web,:Drive,:TEST,:isDeleted) ');
        $colz->bindParam(':Facility', $FACILITY);
        $colz->bindParam(':Adress', $ADRESS);
        $colz->bindParam(':FacilityType', $TYPE);
        $colz->bindParam(':Phone', $PHONE);
        $colz->bindParam(':Web', $WEB);
        $colz->bindParam(':Drive', $DRIVE);
        $colz->bindParam(':TEST', $TEST);
        $colz->bindParam(':isDeleted', $isDeleted);
        $colz->execute();

        $table_fields = $colz->fetchAll(PDO::FETCH_COLUMN);
      } catch (PDOException $e) {
        die('Could not connect to database: ' . $e->getMessage());
      }
      // ====================

      try {
        $cols = $db->prepare('SELECT * from comp353.publichealthcenter ');

        $cols->execute();

        $table_fields = $cols->fetchAll(PDO::FETCH_COLUMN);
      } catch (PDOException $e) {
        die('Could not connect to database: ' . $e->getMessage());
      }

      if (count($results) < count($table_fields)) {
        echo "<h3> The facility, $FACILITY, has been inserted succesfully into the database. Please check the facility display page to verify.</h3>";
      }
    } else
      echo "<h3> $message  </h3>";


    echo "<br>";
    echo "<br>";
    echo "<br>";
  }
  ?>

  <div class="formDiv">
    <h3 class="instruction">Please complete the following information to add a new facility to the database</h3>
    <br>
    <form action="facilityCreate.php" method="POST">

      <br>

      Facility
      <br>
      <input type="text" name="Facility" id="Facility" placeholder="Enter the facility name ...">
      <br>

      <br>
      Address
      <input type="text" id="Adress" name="Adress" placeholder="Enter the address of the facility...">
      <br>

      <br>
      Facility Type
      <br>
      <select id='Type' name='type'>
        <option value="Clinic">Clinic</option>
        <option value="Hospital">Hospital</option>
        <option value="Special Installment">Special Installment</option>
      </select>
      <br>

      <br>
      Phone Number
      <br>
      <input type="text" id="phone" name="phone" placeholder="Enter the phone number of the facility in this format 514-451-2384...">
      <br>

      <br>
      Website
      <input type="text" id="Web" name="Web" placeholder="Enter the website of the facility in this format mthlc.com ...">
      <br>

      <br>
      Driver-Thru Service
      <br>
      <select id='Drive' name='Drive'>
        <option value="Yes">Yes</option>
        <option value="No">No</option>
      </select>
      <br>

      <br>
      Accepting Tests By:
      <br>
      <select id='Test' name='Test'>
        <option value="Appointment">Appointment</option>
        <option value="Both">Both (Walk-In & Appointment)</option>
        <option value="Walk-In">Walk-In</option>
      </select>
      <br>
      
      <br>
      <input type="submit" value="Submit">
    </form>
  </div>

  </body>

</html>
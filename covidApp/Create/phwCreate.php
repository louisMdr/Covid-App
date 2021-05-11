<?php require_once '../database.php';

if (!isset($_SESSION['user_id'])) {
  header('Location: ../login.php');
}
?>

<html>

<head>
  <title>C19PHCS - Create PublicHealthWorker</title>
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

  if (
    isset($_POST['Emp_ID'])   && isset($_POST['Med_Num'])  && isset($_POST['Facility'])  && isset($_POST['Serv_Provided'])

  ) {
    
    $Emp_ID = $_POST['Emp_ID'];
    $Med_Num = $_POST['Med_Num'];
    $Facility_Name = $_POST['Facility'];
    $Serv_Provided = $_POST['Serv_Provided'];

    //=======================================> VERIFICATION OF PUBLICHEALTHWORKER ENTRY
    /**
     * For verification of Med_Num to see if it's in Person yet
     */
    $medNumQuery = $db->prepare('SELECT Med_Num FROM comp353.person');
    $medNumQuery->execute();
    $medNums = $medNumQuery->fetchAll(PDO::FETCH_COLUMN);

    $verif = false;

    $message = "";
    foreach ($medNums as $res) {

      if ($res == $Med_Num) {
        $verif = true;
      }
      else {
        $message = "Please add this person with this $Med_Num as a new entry in the Person relation first before entering their
        work information.";
      }
    }

    /**
     * To verify for uniquess for the employee id
     */
    $empIDQuery = $db->prepare('SELECT Emp_ID FROM comp353.publichealthworker');
    $empIDQuery->execute();
    $empIDs = $empIDQuery->fetchAll(PDO::FETCH_COLUMN);

    $verif2 = true;

    foreach ($empIDs as $res) {

      if ($res == $Emp_ID) {
        $verif2 = false;
        $message = "The employee ID, $Emp_ID, cannot be added to the database because the entry already exists.";
      }
    }

    // if verification is valid then public healh worker entry is not a duplicate 
    if ($verif == true &&  $verif2 == true) {
      try {

        $_empIDQuery = $db->prepare('SELECT EMP_ID FROM comp353.publichealthworker ');
        $_empIDQuery->execute();
        $results = $_empIDQuery->fetchAll(PDO::FETCH_COLUMN);
      } catch (PDOException $e) {
        die('Could not connect to database: ' . $e->getMessage());
      }

      // ====================> INSERTING NEW PUBLICHEALTHWORKER ENTRY
      try {
        $isDeleted = 1;

        $colz = $db->prepare('INSERT Into comp353.publichealthworker 
                              VALUES (:ID,:MED,:Facility,:Services,:isDeleted)');
        $colz->bindParam(':ID', $Emp_ID);
        $colz->bindParam(':MED', $Med_Num);
        $colz->bindParam(':Facility', $Facility_Name);
        $colz->bindParam(':Services', $Serv_Provided);
        $colz->bindParam(':isDeleted', $isDeleted);
        $colz->execute();

        $table_fields = $colz->fetchAll(PDO::FETCH_COLUMN);
      } catch (PDOException $e) {
        die('Could not connect to database: ' . $e->getMessage());
      }
      // ====================

      try {
        $cols = $db->prepare('SELECT * FROM comp353.publichealthworker ');

        $cols->execute();

        $table_fields = $cols->fetchAll(PDO::FETCH_COLUMN);
      } catch (PDOException $e) {
        die('Could not connect to database: ' . $e->getMessage());
      }

      if (count($results) < count($table_fields)) {
        echo "<h3> The worker $Emp_ID  has been inserted succesfully into the database. Please check the public health worker display page to verify.</h3>";
      }
    } else
      echo "<h3> $message </h3>";


    echo "<br>";
    echo "<br>";
    echo "<br>";
  }
  ?>

  <div class="formDiv">
    <h3 class="instruction">Please complete the following information to add a new health worker to the database</h3>
    <br>
    <form action="phwCreate.php" method="POST">
      <br>
      Employee ID
      <br>
      <input type="text" id="Emp_ID" name="Emp_ID" placeholder="Enter the employee ID of the new health worker...">
      <br>

      <br>
      Medical Number
      <input type="text" id="Med_Num " name="Med_Num" placeholder="Enter the medical number of the new health worker...">
      <br>

      <br>
      Facility
      <br>
      <select id="Facility" name="Facility">
        <?php foreach ($facilities  as $result) {

        ?>
          <option value="<?php echo $result ?>"><?php echo $result ?></option>
        <?php } ?>
      </select>
      <br>

      <br>
      Service Provided
      <br>
      <select id='Serv_Provided' name='Serv_Provided'>
        <option value="PCRTest">PCRTest</option>
        <option value="Vaccination">Vaccination</option>
        <option value="BloodTest">BloodTest</option>
      </select>
      <br>
      
      <br>
      <input type="submit" value="Submit">
    </form>
  </div>

  </body>

</html>
<?php require_once '../database.php';

if (!isset($_SESSION['user_id'])) {
  header('Location: ../login.php');
}
?>

<html>

<head>
  <title>C19PHCS - Create Region</title>
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
   * 
   */
  if (isset($_POST['Region'])) {
    
    $Region = $_POST['Region'];

    //=======================================> VERIFICATION OF REGION ENTRY
    /**
     * To verify for uniquess for the region
     */
    $regionQuery = $db->prepare('SELECT Region_Name FROM comp353.Region');
    $regionQuery->execute();
    $regions = $regionQuery->fetchAll(PDO::FETCH_COLUMN);
    $verif = true;

    $message = "";
    foreach ($regions  as $res) {

      if ($res == $Region) {
        $verif = false;
        $message = "The region, $Region, cannot be added to the database because the entry already exists.";
      }
    }

    if ($verif == true) {
      try {

        $_regionQuery = $db->prepare('SELECT Region_Name FROM comp353.Region');
        $_regionQuery->execute();
        $results = $_regionQuery->fetchAll(PDO::FETCH_COLUMN);
      } catch (PDOException $e) {
        die('Could not connect to database: ' . $e->getMessage());
      }

      // ====================> INSERTING NEW REGION ENTRY
      try {
        $isDeleted = 1;

        $colz = $db->prepare('INSERT Into comp353.Region (Region_Name, isDeleted)
                              VALUES (:Region, :isDeleted)');
        
        $colz->bindParam(':Region', $Region);
        $colz->bindParam(':isDeleted', $isDeleted);

        $colz->execute();
      } catch (PDOException $e2) {
        die('Could not connect to database: ' . $e2->getMessage());
      }

      // ====================
      try {
        $cols = $db->prepare('SELECT Region_Name FROM comp353.Region');

        $cols->execute();

        $table_fields = $cols->fetchAll(PDO::FETCH_COLUMN);
      } catch (PDOException $e) {
        die('Could not connect to database: ' . $e->getMessage());
      }

      if (count($results) < count($table_fields)) {
        echo "<h3> The region, $Region, has been inserted succesfully into the database. Please check the region display page to verify.</h3>";
      }
    } else
      echo "<h3> $message </h3>";


    echo "<br>";
    echo "<br>";
    echo "<br>";
  }
  ?>

  <div class="formDiv">
    <h3 class="instruction">Please complete the following information to enter a new region in the database</h3>
    <br>
    <form action="regionsCreate.php" method="POST">

      <br>
      Region
      <br>
      <input type="text" name="Region" id="Region" placeholder="Enter the name of the Region ...">
      <br>

      <br>
      <input type="submit" value="Submit">
    </form>
  </div>

  </body>

</html>
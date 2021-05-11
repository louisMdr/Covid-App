<?php require_once '../database.php';

if (!isset($_SESSION['user_id'])) {
  header('Location: ../login.php');
}
?>

<html>

<head>
  <title>C19PHCS - Add Health Recommendation</title>
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

  if (isset($_POST['Description'])) {
  
    $Description = $_POST['Description'];
   
    //=======================================> VERIFICATION OF PUBLICHEALTHRECOMMENDATION ENTRY
    /**
     * To verify for uniquess for the description
     */
    $descriptionQuery = $db->prepare('SELECT Description FROM comp353.PublicHealthRecommendation');
    $descriptionQuery->execute();
    $descriptions = $descriptionQuery->fetchAll(PDO::FETCH_COLUMN);

    $verif = true;

    foreach ($descriptions as $res) {

      if ($res == $Description) {
        $verif = false;
        $message = "The description, $Description, cannot be added to the database because the entry already exists.";
      }
    }

    // if verif is true then public health recommendation entry is not a duplicate 
    if ($verif == true) {
      try {

        $_descriptionQuery = $db->prepare('SELECT Description FROM comp353.PublicHealthRecommendation');
        $_descriptionQuery->execute();
        $results = $_descriptionQuery->fetchAll(PDO::FETCH_COLUMN);
      } catch (PDOException $e) {
        die('Could not connect to database: ' . $e->getMessage());
      }

      // ====================> INSERTING NEW PUBLICHEALTHRECOMMENDATION ENTRY
      try {
        $isDeleted = 1;
        $colz = $db->prepare('INSERT Into comp353.PublicHealthRecommendation (Description, isDeleted)
                              VALUES (:Description, :isDeleted)');

        $colz->bindParam(':Description', $Description);
        $colz->bindParam(':isDeleted', $isDeleted);

        $colz->execute();
      } catch (PDOException $e2) {
        die('Could not connect to database: ' . $e2->getMessage());
      }
      // ====================

      try {
        $cols = $db->prepare('SELECT Description FROM comp353.PublicHealthRecommendation');

        $cols->execute();

        $table_fields = $cols->fetchAll(PDO::FETCH_COLUMN);
      } catch (PDOException $e) {
        die('Could not connect to database: ' . $e->getMessage());
      }

      if (count($results) < count($table_fields)) {
        echo "<h3 class= \"instruction\"> The description has been inserted successfully into the database. </h3>";
      }
    } else
      echo "<h3> $message </h3>";


    echo "<br>";
    echo "<br>";
    echo "<br>";
  }
  ?>

  <div class="formDiv">
    <h3 class="instruction">Please complete the following information to add a new public health recommendation to the database</h3>
    <br>
    <form action="hrCreate.php" method="POST">

      <br>
      Health Recommendation Description
      <br>
      <textarea class="areaStyle" id="Description" name="Description" placeholder="Enter the description of the new health recommendation.."></textarea>

      <br>

      <input type="submit" value="Submit">
    </form>
  </div>

  </body>

</html>
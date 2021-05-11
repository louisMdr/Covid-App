<?php require_once '../database.php';

if (!isset($_SESSION['user_id'])) {
  header('Location: ../login.php');
}
?>

<html>

<head>
  <title>C19PHCS - Delete Person</title>
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

  if (isset($_POST['Med_Num'])) {

    $Med_Num = $_POST['Med_Num'];

    //=======================================> VERIFICATION OF MEDICAL NUMBER
    /**
     * Verifying if the medical number exists in the database
     */
    $medicalQuery = $db->prepare('SELECT Med_Num FROM comp353.person');
    $medicalQuery->execute();
    $resulty = $medicalQuery->fetchAll(PDO::FETCH_COLUMN);

    $verif = false;

    $message = "";
    foreach ($resulty as $res) {

      if ($res == $Med_Num) {
        $verif = true;
      }
      else {
        $message = "The person with this medical number, $Med_Num, does not exist.";
      }
    }

    // if verif is true then medical number entry exists in database
    if ($verif == true) {
      try {

        $medNumQuery = $db->prepare('SELECT Med_Num FROM comp353.person ');
        $medNumQuery->execute();
        $results = $medNumQuery->fetchAll(PDO::FETCH_COLUMN);
      } catch (PDOException $e) {
        die('Could not connect to database: ' . $e->getMessage());
      }

      // =====================================> SETTING ISDELETED FOR PERSON 
      try {
        $isDeleted = 0;

        $colz = $db->prepare('UPDATE comp353.person c
                              SET c.isDeleted = :isDeleted
                              WHERE c.Med_Num = :Med_Num;');

        $colz->bindParam(':Med_Num', $Med_Num);
        $colz->bindParam(':isDeleted', $isDeleted);
        $colz->execute();

        $table_fields = $colz->fetchAll(PDO::FETCH_COLUMN);
      } catch (PDOException $e) {
        die('Could not connect to database: ' . $e->getMessage());
      }
      // ====================

      echo "<br>";
      echo "<h3 class=\"instruction\"> The person with the medical number, $Med_Num, has been succesfully removed. Please check the person display page to verify.</h3>";

    } else
      echo "<h3 class=\"instruction\"> $message </h3>";

    echo "<br>";
    echo "<br>";
    echo "<br>";
  }
  ?>

  <div class= "formDiv">
    <h3 class="instruction">Please enter the medical number of the person you would like to remove from database</h3>
    <br>
    <form action="personDelete.php" method="POST">

      <br>
      Medical Number
      <br>
      <input type="text" id="Med_Num" name="Med_Num" placeholder="Enter the medical number in this format 1A4M-L3W-C59S...">
      <br>

      <br>
      <input type="submit" value="Delete">
    </form>
  </div>

  </body>

</html>
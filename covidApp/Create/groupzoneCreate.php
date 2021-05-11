<?php require_once '../database.php';

if (!isset($_SESSION['user_id'])) {
  header('Location: ../login.php');
}
?>

<html>

<head>
  <title>C19PHCS - Create GroupZone</title>
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

  if (isset($_POST['Group_Name'])) {
  
    $Group_Name = $_POST['Group_Name'];
   
    //=======================================> VERIFICATION OF GROUPZONE ENTRY
    /**
     * To verify for uniquess for the group_name
     */
    $groupNameQuery = $db->prepare('SELECT Group_Name FROM comp353.GroupZone');
    $groupNameQuery->execute();
    $groupNames = $groupNameQuery->fetchAll(PDO::FETCH_COLUMN);

    $verif = true;

    foreach ($groupNames as $res) {

      if ($res == $Group_Name) {
        $verif = false;
        $message = "The group name, $Group_Name, cannot be added to the database because the entry already exists.";
      }
    }

    // if verif is true then group zone entry is not a duplicate 
    if ($verif == true) {
      try {

        $_groupNameQuery = $db->prepare('SELECT Group_Name from comp353.GroupZone ');
        $_groupNameQuery->execute();
        $results = $_groupNameQuery->fetchAll(PDO::FETCH_COLUMN);
      } catch (PDOException $e) {
        die('Could not connect to database: ' . $e->getMessage());
      }

      // ====================> INSERTING NEW GROUPZONE ENTRY
      try {
        $isDeleted = 1;
        $colz = $db->prepare('INSERT Into comp353.GroupZone (Group_Name, isDeleted) 
                              VALUES (:Group_Name, :isDeleted)');

        $colz->bindParam(':Group_Name', $Group_Name);
        $colz->bindParam(':isDeleted', $isDeleted);

        $colz->execute();
      } catch (PDOException $e2) {
        die('Could not connect to database: ' . $e2->getMessage());
      }
      // ====================

      try {
        $cols = $db->prepare('SELECT Group_Name from comp353.GroupZone');

        $cols->execute();

        $table_fields = $cols->fetchAll(PDO::FETCH_COLUMN);
      } catch (PDOException $e) {
        die('Could not connect to database: ' . $e->getMessage());
      }

      if (count($results) < count($table_fields)) {
        echo "<h3> The groupzone $Group_Name has been inserted succesfully into the database. Please check the groupzone display page to verify.</h3>";
      }
    } else
      echo "<h3> $message </h3>";


    echo "<br>";
    echo "<br>";
    echo "<br>";
  }
  ?>

  <div class="formDiv">
    <h3 class="instruction">Please complete the following information to enter a new group zone in the database</h3>
    <br>
    <form action="groupzoneCreate.php" method="POST">

      <br>
      Group Name
      <input type="text" id="Group_Name" name="Group_Name" placeholder="Enter a name for the new group zone..">
      <br>

      <br>

      <input type="submit" value="Submit">
    </form>
  </div>

  </body>

</html>
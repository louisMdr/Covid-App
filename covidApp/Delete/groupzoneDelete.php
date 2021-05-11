<?php require_once '../database.php';

if (!isset($_SESSION['user_id'])) {
  header('Location: ../login.php');
}
?>

<html>

<head>
  <title>C19PHCS - Delete GroupZone</title>
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
   * Populating the drop down menu for groupzone.
   * 
   */
  $groupZoneQuery = $db->prepare('SELECT Group_Name FROM comp353.groupzone');
  $groupZoneQuery->execute();
  $groupZones = $groupZoneQuery->fetchAll(PDO::FETCH_COLUMN);

  if (isset($_POST['Group_Name'])) {

    $Group_Name = $_POST['Group_Name'];

    //=======================================> VERIFICATION OF GROUP ZONE
    /**
     * Verifying if the group zone exists in the database
     */
    $groupNameQuery = $db->prepare('SELECT Group_Name FROM comp353.groupzone');
    $groupNameQuery->execute();
    $resulty = $groupNameQuery->fetchAll(PDO::FETCH_COLUMN);

    $verif = false;

    $message = "";
    foreach ($resulty as $res) {

      if ($res == $Group_Name) {
        $verif = true;
      }
      else {
        $message = "The group zone, $Group_Name, does not exist.";
      }
    }

    // if verif is true then group zone exists in database
    if ($verif == true) {
      try {

        $_groupNameQuery = $db->prepare('SELECT Group_Name FROM comp353.groupzone');
        $_groupNameQuery->execute();
        $results = $_groupNameQuery->fetchAll(PDO::FETCH_COLUMN);
      } catch (PDOException $e) {
        die('Could not connect to database: ' . $e->getMessage());
      }

      // ===================> Retrieving Group_ID
      try {
        $cols = $db->prepare('SELECT Group_ID 
                              FROM comp353.groupzone r
                              WHERE r.Group_Name = :Group_Name;');

        $cols->bindParam(':Group_Name', $Group_Name);
        $cols->execute();
        $Group_ID = $cols->fetchAll(PDO::FETCH_COLUMN);
      } catch (PDOException $e) {
        die('Could not connect to database: ' . $e->getMessage());
      }

      // =====================================> SETTING ISDELETED FOR GROUPZONE
      try {
        $isDeleted = 0;

        $colz = $db->prepare('UPDATE comp353.groupzone c
                              SET c.isDeleted = :isDeleted
                              WHERE c.Group_ID = :Group_ID;');

        $colz->bindParam(':Group_ID', $Group_ID[0]);
        $colz->bindParam(':isDeleted', $isDeleted);
        $colz->execute();

        $table_fields = $colz->fetchAll(PDO::FETCH_COLUMN);
      } catch (PDOException $e) {
        die('Could not connect to database: ' . $e->getMessage());
      }
      // ====================

      echo "<br>";
      echo "<h3 class=\"instruction\"> The groupzone, $Group_Name, has been succesfully removed. Please check the display page to verify.</h3>";

    } else
      echo "<h3 class=\"instruction\"> $message </h3>";

    echo "<br>";
    echo "<br>";
    echo "<br>";
  }
  ?>

  <div class= "formDiv">
    <h3 class="instruction">Please select the group zone you would like to remove from database</h3>
    <br>
    <form action="groupzoneDelete.php" method="POST">

      <br>
      Group Zone
      <br>
      <select id="Group_Name" name="Group_Name">
        <?php foreach ($groupZones  as $result) {

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
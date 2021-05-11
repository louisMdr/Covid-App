<?php require_once '../database.php';

if (!isset($_SESSION['user_id'])) {
  header('Location: ../login.php');
}
?>

<html>

<head>
  <title>C19PHCS - Delete Region</title>
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
   * Populating the drop down menu for regions
   * 
   */
  $regionQuery = $db->prepare('SELECT Region_Name FROM comp353.region');
  $regionQuery->execute();
  $regions = $regionQuery->fetchAll(PDO::FETCH_COLUMN);

  if (isset($_POST['Region_Name'])) {

    $Region_Name = $_POST['Region_Name'];

    //=======================================> VERIFICATION OF GROUP ZONE
    /**
     * Verifying if the group zone exists in the database
     */
    $_regionQuery = $db->prepare('SELECT Region_Name FROM comp353.region');
    $_regionQuery->execute();
    $resulty = $_regionQuery->fetchAll(PDO::FETCH_COLUMN);

    $verif = false;

    $message = "";
    foreach ($resulty as $res) {

      if ($res == $Group_Name) {
        $verif = true;
      }
      else {
        $message = "The region, $Region_Name, does not exist.";
      }
    }

    // if verif is true then group zone exists in database
    if ($verif == true) {
      try {

        $_regionsQuery = $db->prepare('SELECT Region_Name FROM comp353.region');
        $_regionsQuery->execute();
        $results = $_regionsQuery->fetchAll(PDO::FETCH_COLUMN);
      } catch (PDOException $e) {
        die('Could not connect to database: ' . $e->getMessage());
      }

      // ===================> Retrieving region_ID
      try {
        $cols = $db->prepare('SELECT Region_ID 
                              FROM comp353.region r
                              WHERE r.Region_Name = :Region_Name;');

        $cols->bindParam(':Region_Name', $Region_Name);
        $cols->execute();
        $Region_ID = $cols->fetchAll(PDO::FETCH_COLUMN);
      } catch (PDOException $e) {
        die('Could not connect to database: ' . $e->getMessage());
      }

      // =====================================> SETTING ISDELETED FOR GROUPZONE
      try {
        $isDeleted = 0;

        $colz = $db->prepare('UPDATE comp353.region c
                              SET c.isDeleted = :isDeleted
                              WHERE c.Region_ID = :Region_ID;');

        $colz->bindParam(':Region_ID', $Region_ID[0]);
        $colz->bindParam(':isDeleted', $isDeleted);
        $colz->execute();

        $table_fields = $colz->fetchAll(PDO::FETCH_COLUMN);
      } catch (PDOException $e) {
        die('Could not connect to database: ' . $e->getMessage());
      }
      // ====================

      echo "<br>";
      echo "<h3 class=\"instruction\"> The region, $Region_Name, has been succesfully removed. Please check the display page to verify.</h3>";

    } else
      echo "<h3 class=\"instruction\"> $message </h3>";

    echo "<br>";
    echo "<br>";
    echo "<br>";
  }
  ?>

  <div class= "formDiv">
    <h3 class="instruction">Please select the region you would like to remove from database</h3>
    <br>
    <form action="regionsDelete.php" method="POST">

      <br>
      Region
      <br>
      <select id="Region_Name" name="Region_Name">
        <?php foreach ($regions  as $result) {

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
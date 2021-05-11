<?php require_once '../database.php';

if (!isset($_SESSION['user_id'])) {
  header('Location: ../login.php');
}
?>

<html>

<head>
  <title>C19PHCS - Delete Health Recommendation</title>
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
   * Populating the drop down menu for recommendation_ID
   * 
   */
  $recommendationIDQ = $db->prepare('SELECT Recommendation_ID FROM comp353.publichealthrecommendation');
  $recommendationIDQ->execute();
  $recommendations = $recommendationIDQ->fetchAll(PDO::FETCH_COLUMN);

  if (isset($_POST['Recommendation_ID'])) {

    $Recommendation_ID = $_POST['Recommendation_ID'];

    //=======================================> VERIFICATION OF GROUP ZONE
    /**
     * Verifying if the group zone exists in the database
     */
    $recQuery = $db->prepare('SELECT Recommendation_ID FROM comp353.publichealthrecommendation');
    $recQuery->execute();
    $resulty = $recQuery->fetchAll(PDO::FETCH_COLUMN);

    $verif = false;

    $message = "";
    foreach ($resulty as $res) {

      if ($res == $Recommendation_ID) {
        $verif = true;
      }
      else {
        $message = "The public health recommendation ID, $Recommendation_ID, does not exist.";
      }
    }

    // if verif is true then public health recommendation ID exists in database
    if ($verif == true) {
      try {

        $hrQuery = $db->prepare('SELECT Recommendation_ID FROM comp353.publichealthrecommendation');
        $hrQuery->execute();
        $results = $hrQuery->fetchAll(PDO::FETCH_COLUMN);
      } catch (PDOException $e) {
        die('Could not connect to database: ' . $e->getMessage());
      }

      // =====================================> SETTING ISDELETED FOR GROUPZONE
      try {
        $isDeleted = 0;

        $colz = $db->prepare('UPDATE comp353.publichealthrecommendation c
                              SET c.isDeleted = :isDeleted
                              WHERE c.Recommendation_ID = :Recommendation_ID;');

        $colz->bindParam(':Recommendation_ID', $Recommendation_ID);
        $colz->bindParam(':isDeleted', $isDeleted);
        $colz->execute();

        $table_fields = $colz->fetchAll(PDO::FETCH_COLUMN);
      } catch (PDOException $e) {
        die('Could not connect to database: ' . $e->getMessage());
      }
      // ====================

      echo "<br>";
      echo "<h3 class=\"instruction\"> The public health recommendation ID, $Recommendation_ID, has been succesfully removed. Please check the display page to verify.</h3>";

    } else
      echo "<h3 class=\"instruction\"> $message </h3>";

    echo "<br>";
    echo "<br>";
    echo "<br>";
  }
  ?>

  <div class= "formDiv">
    <h3 class="instruction">Please select the public health recommendation ID you would like to remove from database</h3>
    <br>
    <form action="hrDelete.php" method="POST">

      <br>
      Public Health Recommendation ID
      <br>
      <select id="Recommendation_ID" name="Recommendation_ID">
        <?php foreach ($recommendations  as $result) {

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
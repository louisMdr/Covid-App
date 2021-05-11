<?php require_once '../database.php';

if (!isset($_SESSION['user_id'])) {
  header('Location: ../login.php');
}
?>

<html>

<head>
  <title>C19PHCS - Create Person</title>
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
   * Populating the region array
   */
  $regionQuery = $db->prepare('SELECT Region_Name FROM comp353.region');
  $regionQuery->execute();
  $regions = $regionQuery->fetchAll(PDO::FETCH_COLUMN);

  if (
    isset($_POST['Med_Num'])   && isset($_POST['First_Name'])  && isset($_POST['Last_Name'])  && isset($_POST['Addr'])

    && isset($_POST['Post_Code'])  && isset($_POST['Email'])  && isset($_POST['Tel_Num'])

    && isset($_POST['Date_Birth'])  && isset($_POST['Citizenship'])  && isset($_POST['Gender'])

    && isset($_POST['Parent1'])  && isset($_POST['Parent2'])

  ) {

    $Med_Num = $_POST['Med_Num'];
    $First_Name = $_POST['First_Name'];
    $Last_Name = $_POST['Last_Name'];
    $Addr = $_POST['Addr'];
    $City = $_POST['City'];
    $Post_Code = $_POST['Post_Code'];
    $Region = $_POST['Region'];
    $Email = $_POST['Email'];
    $Tel_Num = $_POST['Tel_Num'];
    $Date_Birth = $_POST['Date_Birth'];
    $Citizenship = $_POST['Citizenship'];
    $Gender = $_POST['Gender'];
    $Parent1 = $_POST['Parent1'];
    $Parent2 = $_POST['Parent2'];

    //=======================================> VERIFICATION OF PERSON ENTRY
    /**
     * To verify for uniquess for the medical number
     */
    $medicalQuery = $db->prepare('SELECT Med_Num FROM comp353.person');
    $medicalQuery->execute();
    $resulty = $medicalQuery->fetchAll(PDO::FETCH_COLUMN);

    $verif = true;

    $message = "";
    foreach ($resulty as $res) {

      if ($res == $Med_Num) {
        $verif = false;
        $message = "The medical number, $Med_Num, cannot be added to the database because the entry already exists.";
      }
    }

    /**
     * To verify for uniquess for the telephone number
     */
    $telephoneQuery = $db->prepare('SELECT Tel_Num FROM comp353.person');
    $telephoneQuery->execute();
    $resultsz = $telephoneQuery->fetchAll(PDO::FETCH_COLUMN);

    $verif2 = true;
    $verif3 = true;

    foreach ($resultsz  as $res) {

      if ($res == $Tel_Num) {
        $verif2 = false;
        $message = "The phone $Tel_Num cannot be added to the database because the entry already exists.";
      }
    }

    // if verif is true then medical number entry is not a duplicate 
    if ($verif == true &&  $verif2 == true) {
      try {

        $medNumQuery = $db->prepare('SELECT Med_Num FROM comp353.person ');
        $medNumQuery->execute();
        $results = $medNumQuery->fetchAll(PDO::FETCH_COLUMN);
      } catch (PDOException $e) {
        die('Could not connect to database: ' . $e->getMessage());
      }

      // ===================> Retrieving region_ID to for new city insertion
      try {
        $cols = $db->prepare('SELECT Region_ID 
                              FROM comp353.region r
                              WHERE r.Region_Name = :Region_Name;');

        $cols->bindParam(':Region_Name', $Region);
        $cols->execute();
        $Region_ID = $cols->fetchAll(PDO::FETCH_COLUMN);
      } catch (PDOException $e) {
        die('Could not connect to database: ' . $e->getMessage());
      }

      // ====================> BEFORE INSERTING PERSON, IF IT'S A NEW CITY, ADD IT FOR REFERENTIAL INTEGRITY CONSTRAINT
      $Province = 'Quebec';
      try {
        $col = $db->prepare('INSERT Into comp353.city (City_Name, Province, Region_ID)
                        SELECT City_Name, Province, Region_ID
                        FROM (SELECT :City_Name AS City_Name, :Province AS Province, :Region_ID AS Region_ID) n
                        WHERE NOT EXISTS (SELECT 1 
                                          FROM comp353.city c 
                                          WHERE c.City_Name = n.City_Name AND c.Province = n.Province AND c.Region_ID = n.Region_ID)');

        $col->bindParam(':City_Name', $City);
        $col->bindParam(':Province', $Province);
        $col->bindParam(':Region_ID', $Region_ID[0]);
        $col->execute();

        $table_fields = $col->fetchAll(PDO::FETCH_COLUMN);
      } catch (PDOException $e) {
        die('Could not connect to database: ' . $e->getMessage());
      }

      // ====================> BEFORE INSERTING PERSON, IF IT'S A NEW POSTAL CODE, ADD IT FOR REFERENTIAL INTEGRITY CONSTRAINT
      try {
        $colo = $db->prepare('INSERT Into comp353.postalcode (Post_Code, City_Name)
                        SELECT Post_Code, City_Name
                        FROM (SELECT :Post_Code AS Post_Code, :City_Name AS City_Name) n
                        WHERE NOT EXISTS (SELECT 1 
                                          FROM comp353.postalcode p 
                                          WHERE p.Post_Code = n.Post_Code AND p.City_Name = n.City_Name)');
        $colo->bindParam(':Post_Code', $Post_Code);
        $colo->bindParam(':City_Name', $City);
        $colo->execute();

        $table_fields = $colo->fetchAll(PDO::FETCH_COLUMN);
      } catch (PDOException $e) {
        die('Could not connect to database: ' . $e->getMessage());
      }

      // ====================> INSERTING NEW PERSON ENTRY
      try {
        $isDeleted = 1;

        $colz = $db->prepare('INSERT Into comp353.person 
	
	VALUES (:Med_Num,:First_Name,:Last_Name,:Addr,:Post_Code,:Email,:Tel_Num,:Date_Birth,:Citizenship,:Gender,:Parent1,:Parent2,:isDeleted)');
        $colz->bindParam(':Med_Num', $Med_Num);
        $colz->bindParam(':First_Name', $First_Name);
        $colz->bindParam(':Last_Name', $Last_Name);
        $colz->bindParam(':Addr', $Addr);
        $colz->bindParam(':Post_Code', $Post_Code);
        $colz->bindParam(':Email', $Email);
        $colz->bindParam(':Tel_Num', $Tel_Num);
        $colz->bindParam(':Date_Birth', $Date_Birth);
        $colz->bindParam(':Citizenship', $Citizenship);
        $colz->bindParam(':Gender', $Gender);
        $colz->bindParam(':Parent1', $Parent1);
        $colz->bindParam(':Parent2', $Parent2);
        $colz->bindParam(':isDeleted', $isDeleted);
        $colz->execute();

        $table_fields = $colz->fetchAll(PDO::FETCH_COLUMN);
      } catch (PDOException $e) {
        die('Could not connect to database: ' . $e->getMessage());
      }
      // ====================
      
      try {
        $cols = $db->prepare('SELECT Med_Num FROM comp353.person ');

        $cols->execute();

        $table_fields = $cols->fetchAll(PDO::FETCH_COLUMN);
      } catch (PDOException $e) {
        die('Could not connect to database: ' . $e->getMessage());
      }

      if (count($results) < count($table_fields)) {
        echo "<br>";
        echo "<h3 class=\"instruction\"> The person with the medical number $Med_Num has been inserted succesfully into the database. Please check the person display page to verify.</h3>";
      }
    } else
      echo "<h3 class=\"instruction\"> $message </h3>";

    echo "<br>";
    echo "<br>";
    echo "<br>";
  }
  ?>

  <div class= "formDiv">
    <h3 class="instruction">Please enter the following information to add a new person to the database</h3>
    <br>
    <form action="personCreate.php" method="POST">

      <br>

      Medical Number
      <br>
      <input type="text" id="Med_Num" name="Med_Num" placeholder="Enter the medical number in this format 1A4M-L3W-C59S...">
      <br>

      <br>
      First Name
      <input type="text" id="First_Name" name="First_Name" placeholder="Enter the first name of the person...">
      <br>

      <br>
      Last Name
      <input type="text" id="Last_Name" name="Last_Name" placeholder="Enter the last name of the person...">
      <br>

      <br>
      Address
      <input type="text" id="Addr" name="Addr" placeholder="Enter the address of the person...">
      <br>

      <br>
      City
      <input type="text" id="City" name="City" placeholder="Enter the city of the person...">
      <br>

      <br>
      Postal Code
      <br>
      <input type="text" id="Post_Code" name="Post_Code" placeholder="Enter the postal code of the person...">
      <br>

      <br>
      Region
      <br>
      <select id="Region" name="Region">
        <?php foreach ($regions  as $_region) {

        ?>
          <option value="<?php echo $_region ?>"><?php echo $_region ?></option>
        <?php } ?>
      </select>
      <br>

      <br>
      Email
      <input type="text" id="Email" name="Email" placeholder="Enter the email of the person...">
      <br>

      <br>
      Telephone
      <input type="text" id="Tel_Num" name="Tel_Num" placeholder="Enter the telephone of the person in this format 514-234-5678...">
      <br>

      <br>
      Date of birth
      <input type="date" id="Date_Birth" name="Date_Birth" class="date">
      <br>

      <br>
      Citizenship
      <input type="text" id="Citizenship" name="Citizenship" placeholder="Enter the citizenship of the person...">
      <br>
      
      <br>
      Gender
      <br>
      <select id='Gender' name='Gender'>
        <option value="Male">Male</option>
        <option value="Female">Female</option>
      </select>
      <br>

      <br>
      Parent 1
      <br>
      <input type="text" id="Parent1" name="Parent1" placeholder="If there is a parent 1 you wish to enter, enter their medical number or enter none...">
      <br>

      <br>
      Parent 2
      <br>
      <input type="text" id="Parent2" name="Parent2" placeholder="If there is a parent 2 you wish to enter, enter their medical number or enter none...">
      <br>

      <br>
      <input type="submit" value="Submit">
    </form>
  </div>

  </body>

</html>
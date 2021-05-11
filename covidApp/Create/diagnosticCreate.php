<?php require_once '../database.php';

if (!isset($_SESSION['user_id'])) {
  header('Location: ../login.php');
}
?>

<html>

<head>
  <title>C19PHCS - Create Diagnostic</title>
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
   * Populating Emp_ID array
   */
  $empIDQuery = $db->prepare('SELECT Emp_ID FROM comp353.publichealthworker');
  $empIDQuery->execute();
  $empIDs = $empIDQuery->fetchAll(PDO::FETCH_COLUMN);

  /**
   * Populating Facility array
   */
  $facilitiyQuery = $db->prepare('SELECT Facility_Name FROM comp353.publichealthcenter');
  $facilitiyQuery->execute();
  $facilities = $facilitiyQuery->fetchAll(PDO::FETCH_COLUMN);



  /**
   * Populating medical number array
   */
  $facilitiyQuery = $db->prepare('SELECT Med_Num FROM comp353.person');
  $facilitiyQuery->execute();
  $result1 = $facilitiyQuery->fetchAll(PDO::FETCH_COLUMN);


  if (
    isset($_POST['Test_ID'])   && isset($_POST['Date_Tested'])  && isset($_POST['Date_Result'])  && isset($_POST['Med_Num'])

    && isset($_POST['PCR_Result'])  && isset($_POST['Emp_ID'])  && isset($_POST['Facility_Name'])
  ) {

    $Test_ID = $_POST['Test_ID'];
    $Date_Tested = $_POST['Date_Tested'];
    $Date_Result = $_POST['Date_Result'];
    $Med_Num = $_POST['Med_Num'];
    $PCR_Result = $_POST['PCR_Result'];
    $Emp_ID = $_POST['Emp_ID'];
    $Facility_Name = $_POST['Facility_Name'];

    //=======================================> VERIFICATION OF DIAGNOSTIC ENTRY
    /**
     * For verification of Test_ID and Med_Num
     */
    $testIDQuery = $db->prepare('SELECT Test_ID FROM comp353.diagnostic');
    $testIDQuery->execute();
    $testIDs = $testIDQuery->fetchAll(PDO::FETCH_COLUMN);

    $count1 = 0;
    $count2 = 0;
    $message = "";

    $verif1 =false;
    $verif2 =false;


    
    foreach ($testIDs  as $res) {
     

      if ($res == $Test_ID) {
        $verif1 =true;
        break;
      }
      $count1++;
    }

    /**
     * For verification of Test_ID and Med_Num
     */
    $medNumQuery = $db->prepare('SELECT Med_Num FROM comp353.diagnostic');
    $medNumQuery->execute();
    $medNums = $medNumQuery->fetchAll(PDO::FETCH_COLUMN);

    foreach ($medNums  as $res) {
     
      if ($res == $Med_Num) {
        $verif2 =true;
  
        break;
      }
      $count2++;
    }

    echo"<br>";
echo"<br>";
$verif3 =false;

if($count2 == $count1 && $count1== count( $testIDs) && $count2 == count($medNums)
&& $verif1 == false && $verif2 == false
)
$verif3 = true;
    
    if ($count2 == $count1 && !$verif3 )
      $message = "The combination, $Test_ID and $Med_Num, cannot be added to the database because the entry already exists.";
    // if verification is valid then diagnostic entry is not a duplicate 
    else {
      try {

        $_testIDQuery = $db->prepare('SELECT Test_ID  FROM comp353.diagnostic ');
        $_testIDQuery->execute();
        $results = $_testIDQuery->fetchAll(PDO::FETCH_COLUMN);
      } catch (PDOException $e) {
        die('Could not connect to database: ' . $e->getMessage());
      }

      // ====================> INSERTING NEW DIAGNOSTIC ENTRY
      try {

       
        echo "<br>";
        $colz = $db->prepare('INSERT Into comp353.diagnostic 
	
	VALUES (:Test_ID,:Date_Tested,:Date_Result,:Med_Num,:PCR_Result,:Emp_ID,:Facility_Name) ');

        $colz->bindParam(':Test_ID', $Test_ID);
        $colz->bindParam(':Date_Tested', $Date_Tested);
        $colz->bindParam(':Date_Result', $Date_Result);
        $colz->bindParam(':Med_Num', $Med_Num);
        $colz->bindParam(':PCR_Result', $PCR_Result);
        $colz->bindParam(':Emp_ID', $Emp_ID);
        $colz->bindParam(':Facility_Name', $Facility_Name);
        $colz->execute();

        $table_fields = $colz->fetchAll(PDO::FETCH_COLUMN);
      } catch (PDOException $e) {
        die('Could not connect to database: ' . $e->getMessage());
      }
      // ====================

      try {
        
        echo "<br>";
        $cols = $db->prepare('SELECT Test_ID  FROM comp353.diagnostic ');

        $cols->execute();

        $table_fields = $cols->fetchAll(PDO::FETCH_COLUMN);
      } catch (PDOException $e) {
        die('Could not connect to database: ' . $e->getMessage());
      }

      if (count($results) < count($table_fields)) {

        echo "<h3> The diagnostic with ID $Test_ID  and medical number $Med_Num has been inserted succesfully into the database. </h3>";

/**
 * Handles the triggers here
 * 
 * --------------------------------------------------------------------------------------------------------------------------------
 */

/**
 * Extracting the Region
 */



/**
 * Extracting the region before the  insert on message
 */
$facilitiesy = $db->prepare('SELECT  Region_Name 
from region,person,postalcode,city
 where person.Post_Code = postalcode.Post_Code
 and City.City_Name =  PostalCode.City_Name
 and City.Region_ID  = Region.Region_ID
 and Med_Num =:Med_Num;
'   
);
$facilitiesy->bindParam(':Med_Num',$Med_Num);
$facilitiesy->execute();

$RegionsArray= $facilitiesy->fetchAll(PDO::FETCH_COLUMN);

$Region = $RegionsArray[0];

  $facilitiesy = $db->prepare('SELECT distinct Level
  from regionhas
  where  Region_ID =:RegionID
  order by Level desc
  '   
);


      if($PCR_Result == "Negative"){
    $Guidelines="Your result has been confirmed as negative, make sure to respect the social distanciation and clean your hands.";
    $Description="You can come back for another test another time.";


/**
 * Getting old and new Alert 
 */
$facilitiesy->bindParam('RegionID',$TheID );
$facilitiesy->execute();

$resultso = $facilitiesy->fetchAll(PDO::FETCH_COLUMN);
  if(count($resultso)!=0)
  $New_AlertState=$resultso[0];
  if(count($resultso)==1 || count($resultso)==0  )
  $Old_AlertState="None";
  else
  $Old_AlertState=$resultso[1];


     /**
* Returns First and Last Name based on medical number
*/

$facilitiesy = $db->prepare('SELECT First_Name ,Last_Name 
  
FROM comp353.Person

where   Med_Num=:Med
'   
);
$facilitiesy->bindParam(':Med',$Med_Num);
$facilitiesy->execute();
$resultso = $facilitiesy->fetchAll(PDO::FETCH_ASSOC);

$Person="";

  /**
   * Extracting the first and last name form the array  and storing them in a variable 
   */
     foreach($resultso as $key=> $result){
        foreach($result as $key2=> $result2){
        $Person = $Person . " ". $result2;
     }
    }

    $Time= date("h:i:sa");

        /**
         * 
         * Guidelines: big paragraph
         *Description: Covid-19 Result: Test Positive. Please fill out symptoms your survey online.
         */
      
      
      $Dates = DateTime::createFromFormat('Y-m-d',$Date_Result);

        $newDate = $Dates->format('Y-m-d');

  
$colz4 = $db->prepare(' INSERT INTO comp353.messages
VALUES (:Dates,:Timess,:Region,:Person,:Email,:OldAlert ,:NewAlert
,:Guidelinesss,:Descriptionsss);
'
); 
             
$colz4 ->bindParam(':Dates',$newDate );
$colz4 ->bindParam(':Timess',$Time);
$colz4 ->bindParam(':Region',$Region);
$colz4 ->bindParam(':Person',$Person);
$colz4 ->bindParam(':Email',$PCR_Result);
$colz4 ->bindParam(':OldAlert',$Old_AlertState);
$colz4 ->bindParam(':NewAlert',$New_AlertState);
$colz4 ->bindParam(':Guidelinesss',$Guidelines);
$colz4 ->bindParam(':Descriptionsss',$Description);
$colz4 ->execute();

      }

      else {
/**
 * Getting old and new Alert 
 */
$facilitiesy->bindParam('RegionID',$TheID );
$facilitiesy->execute();

$resultso = $facilitiesy->fetchAll(PDO::FETCH_COLUMN);
  if(count($resultso)!=0)
  $New_AlertState=$resultso[0];
  if(count($resultso)==1 || count($resultso)==0  )
  $Old_AlertState="None";
  else
  $Old_AlertState=$resultso[1];


     /**
* Returns First and Last Name based on medical number
*/

$facilitiesy = $db->prepare('SELECT First_Name ,Last_Name 
  
FROM comp353.Person

where   Med_Num=:Med
'   
);
$facilitiesy->bindParam(':Med',$Med_Num);
$facilitiesy->execute();
$resultso = $facilitiesy->fetchAll(PDO::FETCH_ASSOC);

$Person="";

  /**
   * Extracting the first and last name form the array  and storing them in a variable 
   */
     foreach($resultso as $key=> $result){
        foreach($result as $key2=> $result2){
        $Person = $Person . " ". $result2;
     }
    }


        $Time= date("h:i:sa");
        $facilitiesy = $db->prepare('SELECT Date_Birth  
        from Person
        where  Med_Num =:Med
       
        '   
      );
    
        $facilitiesy->bindParam('Med',$Med_Num );
        $facilitiesy->execute();
        $facil = $facilitiesy->fetchAll(PDO::FETCH_COLUMN);
        $DateOfBirth = $facil[0];

        /**
         * 
         * Guidelines: big paragraph
         *Description: Covid-19 Result: Test Positive. Please fill out symptoms your survey online.
         */

        $facilitiesy = $db->prepare('SELECT Description  
        from publichealthrecommendation
        '   
      );
        $facilitiesy->execute();
        $facili2 = $facilitiesy->fetchAll(PDO::FETCH_COLUMN);
        
      
        $Guidelines = $facili2[0];
        $Description="Covid-19 Result: Test Positive. Please fill out symptoms your survey online,To fill out the form you need to use your medical number $Med_Num as user name and your date of birth $DateOfBirth  as password.";

      

      $Date = DateTime::createFromFormat('Y-m-d',$Date_Result);

      for($i =0 ; $i < 14 ; $i++){

       
        $newDate = $Date->format('Y-m-d');

  
$colz = $db->prepare(' INSERT INTO comp353.messages
VALUES (:Dates, :Timess,:Region,:Person,:Email,:OldAlert ,:NewAlert
,:Guidelinesss,:Descriptionsss);
'
); 
             
      $colz->bindParam(':Dates',$newDate );
      $colz->bindParam(':Timess',$Time);
      $colz->bindParam(':Region',$Region);
      $colz->bindParam(':Person',$Person);
      $colz->bindParam(':Email',$PCR_Result);
      $colz->bindParam(':OldAlert',$Old_AlertState);
      $colz->bindParam(':NewAlert',$New_AlertState);
      $colz->bindParam(':Guidelinesss',$Guidelines);
      $colz->bindParam(':Descriptionsss',$Description);
      $colz->execute();

      $Date->modify('+1 day');

      }

    }
}


}

if ($message != "")
echo "<h3> $message  </h3>";


echo "<br>";
echo "<br>";
echo "<br>";
      
}


/**
 * -------------------------------------------------------------------------------------------------------------------------------
 */

  ?>

  <div class= "formDiv">
    <h3 class="instruction">Please enter the following information to add a new diagnostic to the database</h3>
    <br>
    <form action="diagnosticCreate.php" method="POST">
      
      <br>
      Test_ID
      <input type="text" id="Test_ID" name="Test_ID" placeholder="Enter the Test of this diagnostic...">
      <br>

      <br>
      Date Tested
      <input type="date" id="Date_Tested" name="Date_Tested" class="date">
      <br>

      <br>
      Date Result
      <input type="date" id="Date_Result" name="Date_Result" class="date">
      <br>

      <br>
      Medical Number
      <select id="Med_Num" name="Med_Num">
      <?php foreach ($result1  as $result) {

      ?>
        <option value="<?php echo $result ?>"><?php echo $result ?></option>
      <?php } ?>
      <br>
      </select>

      <br>
      PCR Result
      <br>
      <select id='PCR_Result' name='PCR_Result'>
        <option value="Negative">Negative</option>
        <option value="Positive">Positive</option>
      </select>
      <br>

      <br>
      Employee ID
      <br>
      <select id="Emp_ID" name="Emp_ID">
        <?php foreach ($empIDs as $result) {

        ?>
          <option value="<?php echo $result ?>"><?php echo $result ?></option>
        <?php } ?>
      </select>
      <br>

      <br>
      Facility Name
      <br>
      <select id="Facility_Name" name="Facility_Name">
        <?php foreach ($facilities as $result) {

        ?>
          <option value="<?php echo $result ?>"><?php echo $result ?></option>
        <?php } ?>
      </select>
      <br>

      <br>
      <input type="submit" value="Submit">
    </form>
  </div>

  </body>

</html>
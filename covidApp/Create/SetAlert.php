<?php require_once '../database.php';

if (!isset($_SESSION['user_id'])) {
  header('Location: ../login.php');
}
?>

<html>

<head>
  <title>C19PHCS - Set new alert</title>
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
   * Populating the drop down menu for regions.
   * 
   */
  $regionQuery = $db->prepare('SELECT Region_Name FROM comp353.region');
  $regionQuery->execute();
  $regions = $regionQuery->fetchAll(PDO::FETCH_COLUMN);

  /**
   * Populating the drop down menu for levels.
   * 
   */
  $levelsQuery = $db->prepare('SELECT * FROM comp353.alert');
  $levelsQuery->execute();
  $levels = $levelsQuery->fetchAll(PDO::FETCH_COLUMN);




  if(  isset($_POST['Region']) ) {


  /*
      $Region2= $_POST['Region'];

    $facilities = $db->prepare('SELECT * FROM comp353.alert');
    $facilities->execute();
    $results2 = $facilities->fetchAll(PDO::FETCH_COLUMN);


  

    $facilities = $db->prepare('SELECT distinct Level 
    FROM comp353.regionhas, comp353.region
    where Region.Region_ID =  regionhas.Region_ID
    and Region_Name = :Region
    order by Level 
    ');
    $facilities->execute();
    $facilities->bingParam(':Region',$Region2);
    $results3 = $facilities->fetchAll(PDO::FETCH_COLUMN);


  '1-Green'
  '2-Yellow'
  '3-Orange'
  '4-Red'


       $limit =  count($results3);

       if($results3[$limit -1]== '4-Red')

           
      $count =0;
    foreach($results2 as $results){
       
         if($results ==  $Level )
         break;
         $count++;
    }

*/



  }







  if (isset($_POST['Region']) && isset($_POST['Alert'])  && isset($_POST['Date'])) {

    $Region = $_POST['Region'];
    $Alert = $_POST['Alert'];
    $Date = $_POST['Date'];

    $verif1 = false;
    $verif2 = false;
    $verif3 = false;

    $count1 = 0;
    $count2 = 0;
    $count3 = 0;

    $value = "";
   

    // // ===================> Retrieving region_ID to for new regionhas insertion
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
       
    //Extracting the region ID 
    $RegionID= $Region_ID[0];

    //=======================================> VERIFICATION OF NEW ALERT ENTRY IN REGIONHAS
    /**
     * For verification of the key in RegionHas
     */
    $_regionQuery = $db->prepare('SELECT Region_ID FROM comp353.regionhas');
    $_regionQuery->execute();
    $_regions = $_regionQuery->fetchAll(PDO::FETCH_COLUMN);

    foreach ($_regions  as $result) {

      if ($result == $RegionID) {
        $verif1 = true;
        break;
      }
      $count1++;
    }

    /**
     * For verification of the key in RegionHas
     */
    $alertDateQuery = $db->prepare('SELECT Alert_Date FROM comp353.regionhas');
    $alertDateQuery->execute();
    $alertDates = $alertDateQuery->fetchAll(PDO::FETCH_COLUMN);

    foreach ($alertDates as $result1) {

      if ($result1 == $Date){
        $verif2 = true;
        break;
      }
      $count2++;
    }

    /**
     * For verification of the key in RegionHas
     */
    $_levelsQuery = $db->prepare('SELECT Level FROM comp353.regionhas');
    $_levelsQuery->execute();
    $_levels = $_levelsQuery->fetchAll(PDO::FETCH_COLUMN);

    foreach ($_levels as $result2) {
      if ($result2 == $Alert){
        $verif3 = true;
      }
        break;
      $count3++;
    }
    $verif4 = false;
    if($count1 == $count2 && $count1 == $count3 && $count1 == $count2 && $verif1==false 
    && $verif2==false   && $verif3==false 
    )
    $verif4 = true;

    if ($count1 == $count2 && $count1 == $count3 && $count1 == $count2 && !$verif4 ) {

      $value = "false";
    }

    if ($value != "false") {
      
      // // ===================> Retrieving region_ID to for new regionhas insertion
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

      // ====================> SETTING NEW ALERT (INSERTING NEW REGIONHAS ENTRY)
      try {

  
        $facilities3 = $db->prepare('INSERT INTO comp353.RegionHas 
                                     VALUES (:Region,:Dates,:Alert)');

        $facilities3->bindParam(':Region',$RegionID);
        $facilities3->bindParam(':Alert', $Alert);
        $facilities3->bindParam(':Dates', $Date);
        $facilities3->execute();
/*
-----------------------------------------------------------------------------------------------------------------------
*/


/***
 * 
 * Handling messages to all people in the same Region
 * 
 */


/**
 * 
 * Returns the first name of all the people who belong in the same region into a list 
 * and also their emails
 * 
 */


try {
  $facilities3 = $db->prepare('SELECT  First_Name
FROM person,region,city,postalcode
where City.City_Name = postalcode.City_Name 
and Region.Region_ID = City.Region_ID
and Person.Post_Code = postalcode.Post_Code
and Region.Region_ID=:RegionID
  ');
  $facilities3->bindParam(':RegionID',$RegionID);
  $facilities3->execute();

  $resultso = $facilities3->fetchAll(PDO::FETCH_COLUMN);

  $FirstNamesArray = array();

  $index=0;

  $Person ="";
  
  /**
   * Filling up the array with the first names of all the people in this region 
   */


     foreach($resultso as $result2){  
     
        $FirstNamesArray[$index]=$result2;
        $index++;    
      }


      /**
 * 
 * Returns the Last names of all the people who belong in the same region into a list 
 * and also their emails
 * 
 */


try {
  $facilities3 = $db->prepare('SELECT  Last_Name
FROM person,region,city,postalcode
where City.City_Name = postalcode.City_Name 
and Region.Region_ID = City.Region_ID
and Person.Post_Code = postalcode.Post_Code
and Region.Region_ID=:RegionID
  ');
  $facilities3->bindParam(':RegionID',$RegionID);
  $facilities3->execute();

  $resultso = $facilities3->fetchAll(PDO::FETCH_COLUMN);

  $LastNamesArray= array();

  $index=0;

  $Person ="";
  
  /**
   * Filling up the array with the Last names of all the people in this region 
   */


 
   $index=0;
     foreach($resultso as $result2){

        $LastNamesArray[$index]=$result2;
        $index++;
      }


        
/**
 * Returns Old alert Level and new Alert Level 
 * 
 */

	$facilitiesy = $db->prepare('SELECT distinct Level
  from regionhas
  where  Region_ID =:RegionID
  order by Level desc
  '   
);
$TheID = $RegionID[0];
  $facilitiesy->bindParam('RegionID',$TheID );
  $facilitiesy->execute();

  $resultso = $facilitiesy->fetchAll(PDO::FETCH_COLUMN);
      
  

       if(count($resultso)!=0  )
    $New_AlertState=$resultso[0];
  
    if(count($resultso)==1 || count($resultso)==0)
    $Old_AlertState="None";
    else
    $Old_AlertState=$resultso[1];

    $Time= date("h:i:sa");
    $Region = $_POST['Region'];
    $Alert = $_POST['Alert'];
    $start_date = $_POST['Date'];

    $Guidelines="The curfew is now at 8pm ,  gyms closed, not indoor gartherings of more than 10  people.";
    $Description = "A new alert has been set for the region $Region please respect the new rules.";

/**
 * Doing the fourteen inserts with a for loop
 */


 $Date = DateTime::createFromFormat('Y-m-d',$start_date);

 $FullNames = array(count($FirstNamesArray));

 /**
  * Putting the names inside  one full name
  */


  $FullNames2 = array();
  
 for($i = 0 ; $i < count($LastNamesArray) ; $i++){

  $FullNames[$i] =  $FirstNamesArray[$i] . " " . $LastNamesArray[$i];

 }



      /**
       * Here we are getting the emails 
       * 
       */

      $facilities3 = $db->prepare('SELECT distinct  email,First_Name
  
      FROM person,region,city,postalcode
      where City.City_Name = postalcode.City_Name 
      and Region.Region_ID = City.Region_ID
      and Person.Post_Code = postalcode.Post_Code
      and Region.Region_ID= :Region;
        ');
        $facilities3->bindParam(':Region',$RegionID);
        $facilities3->execute();
        $resultso = $facilities3->fetchAll(PDO::FETCH_COLUMN);
      
        $EmailsArray = array(count($resultso));
      
        $index=0;
       
      
        count($resultso);
      
      
        /**
         * Filling up the array with the emails of all the people in this region 
         */
           foreach($resultso as $result2){
      
                $EmailsArray[$index]=$result2;
      
          
            $index++;
            /*
            if($index == count($FullNames))
             break;
             */
            }

/*
 $count2 =0;
 for($i = 0 ; $i < count($FirstNamesArray) ; $i++){

  for($i2 = 0 ; $i2 < count($FirstNamesArray) ; $i2++){
         
                if($count2 == 1)
             
   
  }
 }
 */


        for($i = 0 ; $i < count( $FullNames ) ; $i++){
          $newDate = $Date->format('Y-m-d');
          
          try {
            $facilities3 = $db->prepare('INSERT INTO comp353.messages
        VALUES (:Dates, :Timess,:Region,:Person,:Email,:OldAlert ,:NewAlert
      ,:Guidelinesss,:Descriptionsss);
            
            ');

            $facilities3->bindParam(':Dates',$newDate);
            $facilities3->bindParam(':Timess',$Time);
            $facilities3->bindParam(':Region',$Region);
            $facilities3->bindParam(':Person', $FullNames[$i]);
            
            $facilities3->bindParam(':Email', $EmailsArray[$i]);
            $facilities3->bindParam(':OldAlert',$Old_AlertState);
            $facilities3->bindParam(':NewAlert',$New_AlertState);
            $facilities3->bindParam(':Guidelinesss',$Guidelines);
            $facilities3->bindParam(':Descriptionsss',$Description);
            $facilities3->execute();

          //  $Date->modify('+1 day');
               }
          
               catch(PDOException $e)
               {
                 die('Could not connect to database: ' . $e->getMessage());
               }
                
              }
              

              echo "<br>";
              echo "<br>";
              echo "<br>";
             
             echo "<h3> Here is the table  MESSAGES after update  </h3>";
             echo "<br>";
              $facilities = $db->prepare('SELECT * FROM comp353.messages');
               $facilities->execute();
               $results = $facilities->fetchAll(PDO::FETCH_ASSOC);
               if(is_array($results) && count($results) > 0)
               {
                 echo "
               <table border='1'>
               <thead>
                   <tr>";
             
                   $cols = $db->prepare('DESCRIBE comp353.messages');
                 $cols->execute();
                 $table_fields = $cols->fetchAll(PDO::FETCH_COLUMN);
                 foreach ($table_fields as $value)
                 {
                   echo "<th>$value</th>";
                 }
                       
                     /*
                 echo "<th>Nbr of Workers</th>";
             
                 echo "</tr></thead><tbody>";
             */
             
                 foreach($results as $places)
                 {
             
                 echo "<tr>";
                   foreach ($places as $place)
                   {
                     //if its empty
                     if($place == '')
                     {
                       echo "<td>None</td>";
                     }
                     else
                     {
                       echo "<td>'$place'</td>";
                     }
                   }
                 echo "</tr>";	
                 }
               }
               echo "</tbody>
               </table>";

}
catch(PDOException $e)
{
  die('Could not connect to database: ' . $e->getMessage());
}
  
  }
  catch(PDOException $e)
  {
    die('Could not connect to database: ' . $e->getMessage());
  }

  echo "<h3> The alert $Alert for the region $Region has been added successfuly.<h3>";
}

catch(PDOException $e)
  {
    die('Could not connect to database: ' . $e->getMessage());
  }

}

else
echo "<h3> The alert $Alert for the region $Region on $Date already exists in the database.<h3>";

  }


/*
-----------------------------------------------------------------------------------------------------------------------
*/

  ?>

  </body>

  <div class="formDiv">
    <h3 class="instruction">Please enter the following information to set up a new alert</h3>
    <br>

  

   


    <form action="SetAlert.php" method="POST">

      <br>

      Region
      <select id="Region" name="Region">
        <?php foreach ($regions  as $result) {

        ?>
          <option value="<?php echo $result ?>"><?php echo $result ?></option>
        <?php } ?>
      </select>
    
    <br>
    
      Level
      <select id="Alert" name="Alert">
        <?php foreach ($levels  as $result2) {

        ?>
          <option value="<?php echo $result2 ?>"><?php echo $result2 ?></option>
        <?php } ?>
      </select>
      <br>

      <br>
      Date
      <input type="date" id="Date" name="Date" class="date">
      <br>

      <br>
      <input type="submit" value="Submit">
    </form>
  </div>


</html>
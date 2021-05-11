
<?php require_once '../database.php';

if(!isset($_SESSION['user_id']) || $_SESSION['type'] == "Worker")
{
    header('Location: ../login.php');
}

?>

<html>
<head>
    <title>C19PHCS - Survey Form</title>
    <link rel="stylesheet" href="../style.css">
</head>
<?php include('../Person/dropdownmenu.html');

//set default values
date_default_timezone_set("America/New_York");

//select pos_case ID:
$posID = $db->prepare('SELECT MAX(PositiveCase_ID) FROM comp353.positivecase WHERE Med_Num = :Med_Num');
$posID->bindParam(':Med_Num', $_SESSION['user_id']);
$posID->execute();
$posCaseID = $posID->fetch(PDO::FETCH_ASSOC);
$posCaseID = $posCaseID['MAX(PositiveCase_ID)'];
?>
    <br><br>
    <div class="formDiv">
    <h3 class="instruction">Please fill up this form:</h3><br>
    <form action="surveyform.php" method="POST">
        <div class="questions">
            <label for="date">Choose a date*:</label>
            <input type="date" name = "date" value="<?php echo date('Y-m-d');?>" required/>
        </div>
        <div class="questions">
            <br>
            <label for="time">Choose a time*:</label>
            <input type="time" name="time" value = "<?php echo date("H:i");?>" required/>
        </div>
        <div class="questions">
            <br>
            <p>Select symptoms*:</p><br>
            <input type="checkbox" name="sympt1"/> Fever<br>
            <input type="checkbox" name="sympt2"/> Cough<br>
            <input type="checkbox" name="sympt3"/> Shortness of breath or difficulty breathing<br>
            <input type="checkbox" name="sympt4"/> Loss of taste and smell<br>
            <input type="checkbox" name="sympt5"/> Nausea<br>
            <input type="checkbox" name="sympt6"/> Stomach aches<br>
            <input type="checkbox" name="sympt7"/> Vomiting<br>
            <input type="checkbox" name="sympt8"/> Headache<br>
            <input type="checkbox" name="sympt9"/> Muscle pain<br>
            <input type="checkbox" name="sympt10"/> Diarrhea<br>
            <input type="checkbox" name="sympt11"/> Sore throat<br>
            <br>
            <p>Other Symptoms:</p>
            <input type="text" name="sympt12"/><br>
        </div>
        <div class="questions">
        <br>
            <label for="temperature">Enter temperature*:</label>
            <input type="text" name="temperature" required>
        </div>
    </div>
        <!-- this goes to the up php code -->
        <input type="submit" name="form_send" value="Submit"/>
        <br><br>
    </form>

<?php 
if(isset($_POST['form_send']))
    {
        //check number of days left:
        $daysMinMax = $db->prepare('SELECT MIN(FollowUp_Date), MAX(FollowUp_Date) FROM comp353.personhas WHERE PositiveCase_ID = :posID');
        $daysMinMax->bindParam(':posID', $posCaseID);
        $daysMinMax->execute();

        $resultMinMax = $daysMinMax->fetch(PDO::FETCH_ASSOC);
        //days difference
        $diff = $db->prepare('SELECT DATEDIFF(:minDate ,:maxDate) as dateDiff');
        $diff->bindParam(':minDate', $resultMinMax['MAX(FollowUp_Date)']);
        $diff->bindParam(':maxDate', $resultMinMax['MIN(FollowUp_Date)']);
        $diff->execute();
        $resultDiff = $diff->fetch(PDO::FETCH_ASSOC); 
        if($resultDiff['dateDiff'] < 13)
        {
            //insert an entry for each symptom
            for ($i=0; $i < 11; $i++)
            { 
                $symptom = ($i+1);
                $index = "sympt" . $symptom;
                if(isset($_POST[$index]))
                {
                $insertValue = $db->prepare('INSERT INTO comp353.personhas VALUES (:posID, :date,:type)');
                $insertValue->bindParam(':posID', $posCaseID);
                $insertValue->bindParam(':date', $_POST["date"]);
                $insertValue->bindParam(':type', $symptom);
                $insertValue->execute();
                }
            }

            //if symptom is new - add to database + temperature value
            if(strlen($_POST['sympt12']) > 0)
            {
                $descript = $_POST['sympt12'] . " (temperature at " . $_POST['temperature'] . ", time:" . $_POST['time'] .  ")";
                $add = $db->prepare('INSERT INTO comp353.symptoms (Description) VALUES (:valuee)');
                $add->bindParam(':valuee', $descript);
                $add->execute();
                if($add)
                {
                    echo "<p>New symptom added.</p>";
                }
                else
                {
                    echo "<p>Error: Entering new symptom failed.</p>";
                }

                //get recently added symptom index
                $maxIndex = $db->prepare('SELECT MAX(Symptom_ID) FROM comp353.symptoms');
                $maxIndex->execute();
                $maxIndex = $maxIndex->fetch(PDO::FETCH_ASSOC);
                $maxIndex = $maxIndex['MAX(Symptom_ID)'];

                //add new symptom to patient
                $insertValue = $db->prepare('INSERT INTO comp353.personhas VALUES (:posID, :date,:type)');
                $insertValue->bindParam(':posID', $posCaseID);
                $insertValue->bindParam(':date', $_POST["date"]);
                $insertValue->bindParam(':type', $maxIndex);
                $insertValue->execute();

            }

            echo "<p>Form submitted successful.</p>";
        }
        else
        {
            echo "<p>Error: You have completed the 14 days.</p>";
        }

    }

?>

</body>
</html>
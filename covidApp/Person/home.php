<?php require_once '../database.php';

// print("Debugging purposes:<br>");
// print_r($_SESSION);
// print_r($_POST);

if(!isset($_SESSION['user_id']) || $_SESSION['type'] == "Worker")
{
	header('Location: ../login.php');
}
?>

<html>
<head>
	<title>C19PHCS - Patient</title>
<?php include('dropdownmenu.html'); ?>

<br><br>
<h1>Welcome
<?php
$name = $db->prepare('SELECT First_Name FROM comp353.person WHERE Med_Num = :med_nbr');
$name->bindParam(':med_nbr', $_SESSION['user_id']);
$name->execute();
$result = $name->fetch(PDO::FETCH_ASSOC);
echo $result['First_Name'];
?>,</h1>


</body>
</html>


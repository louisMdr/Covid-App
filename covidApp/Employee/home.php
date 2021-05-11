<?php require_once '../database.php';



if(!isset($_SESSION['user_id']) || $_SESSION['type'] == "Person")
{
	header('Location: ../login.php');
}?>

<html>
    <head>
        <title>C19PHCS - Homepage</title>
        <link rel="stylesheet" href="../style.css">
    </head>
<body>

<?php include('dropdownmenu.html');?>

<br><br>
<h1>Welcome
<?php
$name = $db->prepare('SELECT First_Name FROM comp353.person WHERE Med_Num = :med_nbr');
$name->bindParam(':med_nbr', $_SESSION['user_id']);
$name->execute();
$result = $name->fetch(PDO::FETCH_ASSOC);
echo $result['First_Name'];
?>!</h1>

<h2>Please follow safety guidelines and stay safe!</h2>

</body>
</html>
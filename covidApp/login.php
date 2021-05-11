<?php require_once 'database.php';

if(isset($_SESSION['user_id']))
{
	$_SESSION['logInFail'] = "";
	//NEED TO CHANGE THIS
	if($_SESSION['type'] == "Worker")
	{
		header("Location: Employee/home.php");
	}
	else
	{
		header("Location: Person/home.php");
	}
}


if(!empty($_POST['username']) && !empty($_POST['password']))
{
	//seperate table "users" (not allowed)
	// $records = $db->prepare('SELECT user_id, user_name, user_password, user_status FROM comp353.users WHERE user_name = :user_name AND user_status != "Deactive"');
	//! also changed inactive status thing

	$records = $db->prepare('SELECT First_Name, Med_Num, Date_Birth FROM comp353.person WHERE Med_Num = :Med_Num');


	$records->bindParam(':Med_Num', $_POST['username']);
	$records->execute();
	$results = $records->fetch(PDO::FETCH_ASSOC);
	
	$_SESSION['logInFail'] = "";

	if(is_array($results) && count($results) > 0 && $_POST['password'] == $results['Date_Birth'])
	{
		//know user is logged in
		$_SESSION['user_id'] = $results['Med_Num'];
		$_SESSION['username'] = $results['First_Name'];
		
		//if you're a health worker
		$job1 = $db->prepare('SELECT * FROM comp353.publichealthworker WHERE Med_Num = :Med_Num');
		$job1->bindParam(':Med_Num', $_SESSION['user_id']);
		$job1->execute();
		$result1 = $job1->fetch(PDO::FETCH_ASSOC);

		$job2 = $db->prepare('SELECT * FROM comp353.positivecase WHERE  Med_Num = :Med_Num');
		$job2->bindParam(':Med_Num', $_SESSION['user_id']);
		$job2->execute();
		$result2 = $job2->fetch(PDO::FETCH_ASSOC);

		if($result1)
		{
			$_SESSION["type"] = "Worker";
			header("Location: Employee/home.php");
		}
		else if($result2)
		{
			$_SESSION["type"] = "Person";
			header("Location: Person/surveyform.php");
		}
		else
		{
			$_SESSION['logInFail'] = "Sorry, you are not an employee or a positive case patient.";
		}

	}
	else
	{
	//since its not a value in sql or inactive account, wrong info
	$_SESSION['logInFail'] = "Sorry, those credentials do not match! Please try again.";
	}
}
?>
<html>
<head>
	<link rel="stylesheet" href="style.css">
	<title>C19PHCS - Log In</title>
</head>
<body>

</body>
</html>
<br><br>
<h1 align="center">COVID-19 Public Health Care System (C19PHCS)</h1>
<br><br>
<div class="loginDiv" align="center">
	<form action="login.php" method="POST">
		
		<h2>Log In</h2><br>
		<br>
		<div>
			<label for="username">Username:</label>
		</div>
			<input type="text" class="loginInput" name="username" required>
		<br>
		<br>
		<div>
			<label for="password">Password:</label>
		</div>
			<input type="password" class="loginInput" name="password" required>
		<br>
		<br>
		<!-- this goes to the up php code -->
		<input type="submit" name="login_user" id="loginSubmit" value="Log In"/>

		<p><?php echo $_SESSION['logInFail']; ?></p>
		<!-- <p>Not a user? <a href="registration.php">Register</a></p> -->
	</form>

</div>
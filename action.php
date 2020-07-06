<?php 
	$mysqli = mysqli_connect('mysql.zzz.com.ua', 'gluedrinker', 'TksDq2B6', 'gluedrinker');
	
	$authUserName = $_GET['authUserName'];
	$inputPassword = $_GET['inputPassword'];
	$authButton = $_GET['authButton'];
	
	
		if (($inputPassword == "123") && ($authUserName == "admin")) {
			header("Location: admin.php");
		}
		else {
			echo "<script>alert('Неверно введены данные')</script>";
		}
	include("index.php");
 ?>
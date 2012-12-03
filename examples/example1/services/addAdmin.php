<?php
require_once '../base/include_top.php';
if(!isset($_SESSION['admin'])) {
	header("Location: ../index.php?error=".urlencode("Admin permission required"));
	exit;
}	

$error = null;

if(isset($_POST['user']) && isset ($_POST['passwd'])) {
	$usr = $_POST['user'];
	$passwd = $_POST['passwd'];
	try {
		$admin = new Admin();
		$admin->setUsername($usr);
		$admin->setPassword($passwd);
		$admin->insert();

		header('Location: ../admin.php');
		exit;
	}
	catch(Exception $e) {
		$error = "Username already exist";
	}
}
else {
	$error = "Username/password has not reached us.";
}
header ("Location: ../admin.php?error=".urlencode($error));
?>
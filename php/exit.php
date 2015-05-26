<?php
	session_start();
	$_SESSION['iduser'] = null;
	header('Location: ../index.php', true, 303);
?>
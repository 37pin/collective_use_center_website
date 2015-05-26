<?php
	session_start();
	$_SESSION['iduser'] = null;
	session_destroy();
	header('Location: ../index.php', true, 303);
?>
<?php
	require 'connect.php';
	session_start();
	$conn = connect::getConnection();
	
	$idUser = $_SESSION['iduser'];
	$query = "SELECT idPatient FROM patients WHERE idUser = $idUser";
	$result = $conn->query($query);
	$row = $result->fetch(PDO::FETCH_ASSOC);
	$idPatient = $row['idPatient'];
	
	$fio = $_POST['fio-doctor'];
	$dateSurvey = $_POST['date'];
	$timeBegin = $_POST['timeBegin'];
	$timeEnd = $_POST['timeEnd'];
	
	list($surname, $name, $lastname) = preg_split("/ /", $fio);
	$query = "SELECT idUser";
	$query .= " FROM users";
	$query .= " WHERE surname = '$surname'";
	$query .= " AND name = '$name'";
	$query .= " AND lastname = '$lastname'";
	$result = $conn->query($query);
	$row = $result->fetch(PDO::FETCH_ASSOC);
	$idUser = $row['idUser'];
	
	$query = "SELECT idDoctor FROM doctors WHERE idUser = $idUser";
	$result = $conn->query($query);
	$row = $result->fetch(PDO::FETCH_ASSOC);
	$idDoctor = $row['idDoctor'];
	
	$query = 'SELECT COUNT(iddoctor) AS cnt';
	$query .= ' FROM surschedule';
	$query .= " WHERE iddoctor = $idDoctor";
	$query .= " AND date_survey = '$dateSurvey'";
	$query .= " AND (('$timeBegin' > time_begin AND '$timeBegin' < time_end) OR ('$timeEnd' > time_begin AND '$timeEnd' < time_end))";
	$result = $conn->query($query);
	$row = $result->fetch(PDO::FETCH_ASSOC);
	$cnt = $row['cnt'];
	if ($cnt > 0)
		die("error");
	
	$query = "INSERT INTO surschedule(idpatient, iddoctor, date_survey, time_begin, time_end) VALUES ($idPatient, $idDoctor, '$dateSurvey', '$timeBegin', '$timeEnd')";
	$result = $conn->prepare($query);
	$result->execute();
	echo "good";
?>
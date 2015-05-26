<?php
	session_start();
	require 'php\connect.php';
	require 'php\check.php';
	
	$idUser = $_SESSION['iduser'];
	if (empty($idUser)) {
		header('Location: signin.php', true, 303);
		die();
	}

	$conn = connect::getConnection();
	$query = "SELECT type FROM users WHERE iduser = $idUser";
	$result = $conn->query($query);
	$row = $result->fetch(PDO::FETCH_ASSOC);
	$type = $row['type'];
	if ($type != "employee") {
		header('Location: index.php', true, 303);
		die();
	}
	
	if (isset($_POST['add-survey'])) {
		$fioPatient = $_POST['fio-patient'];
		$fioDoctor = $_POST['fio-doctor'];
		$conclusion = $_POST['conclusion'];
		$date = $_POST['date'];
		$fioPatient = checkLabel($fioPatient);
		$fioDoctor = checkLabel($fioDoctor);
		$conclusion = checkLabel($conclusion);
		$date = checkLabel($date);
		
		if (!empty($fioPatient) || !empty($fioDoctor) || !empty($date) || !empty($conclusion)) {
			list($surname, $name, $lastname) = preg_split('/ /', $_POST['fio-patient']);
			
			$query = "SELECT idPatient ";
			$query .= "FROM patients, users ";
			$query .= "WHERE patients.idUser = users.idUser AND ";
			$query .= "users.surname = '$surname' AND ";
			$query .= "users.lastname = '$lastname' AND ";
			$query .= "users.name = '$name'; ";
			//die($query);
			$result = $conn->query($query);
			$row = $result->fetch(PDO::FETCH_ASSOC);
			$idPatient = $row['idPatient'];
			
			list($surname, $name, $lastname) = preg_split('/ /', $_POST['fio-doctor']);
			$query = "SELECT idDoctor ";
			$query .= "FROM doctors, users ";
			$query .= "WHERE doctors.idUser = users.idUser AND ";
			$query .= "users.surname = '$surname' AND ";
			$query .= "users.lastname = '$lastname' AND ";
			$query .= "users.name = '$name'; ";
			$result = $conn->query($query);
			$row = $result->fetch(PDO::FETCH_ASSOC);
			$idDoctor = $row['idDoctor'];
			
			$query = "INSERT INTO surveys ";
			$query .= "(idPatient, idUser, idDoctor, dateSurvey, conclusion)";
			$query .= "VALUES ";
			$query .= "($idPatient, $idUser, $idDoctor, '$date', '$conclusion')";
			$result = $conn->prepare($query);
			$result->execute();			
		}
	}
	
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Личный кабинет - Добавить результаты обследования</title>
		<meta charset="utf-8">
		<link rel="stylesheet" href="css/style-main.css">
		<link rel="stylesheet" href="css/style-content.css">
	</head>
	
	<body>
		<header>
			<div class="logo-header">
				<a href="index.php">
					<img src="image/emblem.png" alt="Центр коллективного пользования по созданию мультимедиа контента АГУ">
				</a>
			</div>
			<div class="title-org">
				Центр коллективного пользования по созданию мультимедиа контента АГУ
			</div>
			<div class="contact-phone">
				Телефон для справок: +7 (8512) 59‒52‒34
			</div>
			<nav class="first-navigation">
				<li>
					<a href="index.php">Главная</a>
				</li>
				<li>
					<a href="personnel.php">Сотрдники</a>
				</li>
				<li>
					<a href="contacts.php">Контакты</a>
				</li>
				<li>
					<a href="signin.php">Профиль</a>
				</li>
			</nav>
		</header>
		
		<div id="content">
			<div class="container">
				<h1>Личный кабинет - Добавить результаты обследования</h1>
			</div>
			<nav class="experience">
				<li>
					<a href="employee-adding-patient.php">Добавить пациента</a>
				</li>
				<li>
					<a href="employee-adding-result-survey.php">Добавить результаты обследования</a>
				</li>
				<li>
					<a href="php\exit.php">Выход</a>
				</li>
			</nav>
			<div class="main-section">
				<div class="container">
					<form method="post" action="employee-adding-result-survey.php">
						<input name="fio-patient" type="text" placeholder="Введите ФМО пациента"><br>
						<input name="fio-doctor" type="text" placeholder="Введите ФМО врача"><br>
						<input name="date" type="date"><br>
						<textarea name="conclusion" placeholder="Введите заключение"></textarea><br>
						<input name="add-survey" type="submit" value="Добавить">
					</form>
				</div>
			</div>
		</div>
		
		<footer>
			<div class="logo-footer">
				<a href="index.php">
					<img src="image/emblem.png" alt="Центр коллективного пользования по созданию мультимедиа контента АГУ" >
				</a>
			</div>
			
			<section>
				<p>+7 (8512) 59‒52‒34</p>
				<p>&copy; 2015 АГУ</p>
			</section>
			<section>
				<p>Пн - Пт</p>
				<p>9:00 - 17:30</p>
			</section>
			<section>
				<p>Сб - Вс</p>
				<p>Выходной</p>
			</section>
		</footer>
	
	</body>
</html>
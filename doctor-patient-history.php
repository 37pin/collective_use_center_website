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
	if ($type != "doctor") {
		header('Location: index.php', true, 303);
		die();
	}
	
	if (isset($_POST['show-history'])) {
		$_POST['fio-patient'] = checkLabel($_POST['fio-patient']);
		list($surname, $name, $lastname) = preg_split('/ /', $_POST['fio-patient']);
		$query = 'SELECT idPatient ';
		$query .= 'FROM patients, users ';
		$query .= 'WHERE patients.idUser = users.idUser AND ';
		$query .= "LOWER(users.surname) = LOWER('$surname') AND ";
		$query .= "LOWER(users.lastname) = LOWER('$lastname') AND ";
		$query .= "LOWER(users.name) = LOWER('$name')";
		
		$result = $conn->query($query);
		$row = $result->fetch(PDO::FETCH_ASSOC);
		$idPatient = $row['idPatient'];

		$str = "";
		$query = "SELECT COUNT(*) FROM surveys WHERE idPatient = $idPatient";
		if ($result = $conn->query($query)) {
			if ($result->fetchColumn() > 0) {
				$query = 'SELECT datesurvey, conclusion, diagnosis ';
				$query .= 'FROM surveys ';
				$query .= "WHERE idPatient = $idPatient ";
				$query .= 'ORDER BY datesurvey DESC';
				$result = $conn->query($query);
				while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
					$str .= "<div class=\"user-survey-results\">";
					$str .= '	<p>Дата обследования: '.$row['datesurvey'].'</p>';
					$str .= '	<p>Заключение:<br> ';
					$str .= $row['conclusion'].'</p>';
					$str .= '	<p>Диагноз:<br> ';
					$str .= $row['diagnosis'].'</p>';
					$str .= '</div>';
				}
			} else
				$str = 'Пациент ещё не проходил обследование';
		}
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Личный кабинет - Просмотр результатов обследования</title>
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
					<a href="personnel.php">Сотрудники</a>
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
				<h1>Личный кабинет - Просмотр результатов обследования</h1>
			</div>
			<nav class="experience">
				<li>
					<a href="doctor-schedule.php">Сформировать расписание</a>
				</li>
				<li>
					<a href="doctor-schedule-see.php">Просмотреть расписание</a>
				</li>				
				<li>
					<a href="doctor-results.php">Просмотреть результаты обследования</a>
				</li>
				<li>
					<a href="doctor-patient-history.php">Просмотр истории пациента</a>
				</li>
				<li>
					<a href="php/exit.php">Выход</a>
				</li>
			</nav>
			<div class="main-section">
				<div class="container">
					<form method="post" action="doctor-patient-history.php">
						<input name="fio-patient" type="text" placeholder="Введите ФИО пациента"><br>
						<input name="show-history" type="submit" value="Показать историю" style="width: 150px;">
					</form>
				</div>
				<div class="main-text">
					<?=$str;?>
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
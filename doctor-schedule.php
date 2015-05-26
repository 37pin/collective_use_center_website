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
	
	if (isset($_POST['add-schedule'])) {
		$date = $_POST['date'];
		$timeBegin = $_POST['time-begin'];
		$timeEnd = $_POST['time-end'];
		$date = checkLabel($date);
		$timeBegin = checkLabel($timeBegin);
		$timeEnd = checkLabel($timeEnd);
		if (!empty($date) && !empty($timeBegin) && !empty($timeEnd)) {
			$query = "SELECT iddoctor FROM doctors WHERE iduser = $idUser";
			$result = $conn->query($query);
			$row = $result->fetch(PDO::FETCH_ASSOC);
			$idDoctor = $row['iddoctor'];
			
			$query = "INSERT INTO docschedule ";
			$query .= "(iddoctor, daydate, time_begin, time_end) ";
			$query .= "VALUES ($idDoctor, '$date', '$timeBegin', '$timeEnd')";
			$result = $conn->prepare($query);
			$result->execute();
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
				<h1>Личный кабинет - Формирование расписания</h1>
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
					<form method="post" action="doctor-schedule.php">
						<p>Выберите дату</p>
						<input name="date" type="date"><br>
						<p>Выберите время начала приема</p>
						<input name="time-begin" type="time"><br>
						<p>Выберите время окончание приема</p>
						<input name="time-end" type="time"><br>
						<input name="add-schedule" type="submit" value="Добавить">
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
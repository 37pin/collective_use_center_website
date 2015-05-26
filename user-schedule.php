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
	if ($type != "patient") {
		header('Location: index.php', true, 303);
		die();
	}
	
	$query = "SELECT idPatient FROM patients WHERE idUser = $idUser";
	$result = $conn->query($query);
	$row = $result->fetch(PDO::FETCH_ASSOC);
	$idPatient = $row['idPatient'];
		
	$translate = array(
		"Mon" => "Понедельник",
		"Tue" => "Вторник",
		"Wed" => "Среда",
		"Thu" => "Четверг",
		"Fri" => "Пятница",
		"Sat" => "Суббота",
		"Sun" => "Воскресенье",
	);
	$str = "";
	$i = 1;
	$query = "SELECT *"
		. " FROM surschedule"
		. " WHERE idPatient = $idPatient"
		. " AND date_survey >= CURDATE()"
		. " AND time_begin >= CURTIME()"
		. " ORDER BY date_survey, time_begin";
	$result = $conn->query($query);
	while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
		$dayDate = $row['date_survey'];
		$day = strftime("%a", strtotime($dayDate));
		$day = $translate[$day];

		$str .= "<div class=\"user-survey-results\">"
			. "<p>$dayDate  $day</p>";
		$str .= "<p>Порядковый номер оследования: $i</p>";
		$str .= "<p>Время начала обследования: C ".$row['time_begin']." До ".$row['time_end']."</p>";
		$str .= "</div>";
		$i++;
	}
	if ($str == "")
		$str = "Вы ещё не создал расписание";

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
				<h1>Личный кабинет - Просмотр расписания</h1>
			</div>
			<nav class="experience">
				<li>
					<a href="user-info.php">Просмотр персональных данных</a>
				</li>
				<li>
					<a href="user-schedule.php">Просмотреть расписание</a>
				</li>				
				<li>
					<a href="user-appointments.php">Запись на прием к врачу</a>
				</li>
				<li>
					<a href="user-results.php">Просмотр результатов исследований</a>
				</li>
				<li>
					<a href="php/exit.php">Выход</a>
				</li>
			</nav>
			<div class="main-text main-section">
				<?=$str;?>
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
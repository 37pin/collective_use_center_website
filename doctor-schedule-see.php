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
	
	$query = "SELECT idDoctor FROM doctors WHERE idUser = $idUser";
	$result = $conn->query($query);
	$row = $result->fetch(PDO::FETCH_ASSOC);
	$idDoctor = $row['idDoctor'];
	
	$query = "SELECT daydate, time_begin, time_end";
	$query .= " FROM docschedule";
	$query .= " WHERE iddoctor = $idDoctor";
	$query .= " AND daydate >= CURDATE()";
	$query .= " ORDER BY daydate";
	$query .= " LIMIT 7";
//die($query);
	$result = $conn->query($query);
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
	while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
		$dayDate = $row['daydate'];
		
		$day = strftime("%a", strtotime($dayDate));
		$day = $translate[$day];
		
		$query = "SELECT COUNT(*)"
			. " FROM surschedule"
			. " WHERE iddoctor = $idDoctor"
			. " AND date_survey = '$dayDate'"
			. " AND time_begin >= CURTIME()"
			. " ORDER BY time_begin";
		
		$result2 = $conn->query($query);
		if ($result2->fetchColumn() > 0) {
			
			$query = "SELECT time_begin, time_end"
				. " FROM surschedule"
				. " WHERE iddoctor = $idDoctor"
				. " AND date_survey = '$dayDate'"
				. " AND time_begin >= CURTIME()"
				. " ORDER BY time_begin";
			$result2 = $conn->query($query);
			$str .= "<div class=\"user-survey-results\">"
				. "<p>$dayDate  $day</p>";
			$i = 1;
			while ($row2 = $result2->fetch(PDO::FETCH_ASSOC)) {
				$str .= "<p>Порядковый номер пациента: $i</p>";
				$str .= "<p>Время начала обследования: C ".$row2['time_begin']." До ".$row2['time_end']."</p>";
				$str .= "<hr>";
				$i++;
			}
		$str .= "</div>";
		} else {
			$str .= "<div class=\"user-survey-results\">"
				. "<p>$dayDate  $day</p>"	
				. "<p>Нет обследований</p>"
				. "</div>";
		}
	}
	$str = $str == "" ? "Вы ещё не создал расписание" : $str;
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
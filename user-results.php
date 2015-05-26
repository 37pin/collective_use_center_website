<?php
	session_start();
	require 'php\connect.php';
	
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

	
	$query = "SELECT idPatient FROM patients WHERE iduser = $idUser";
	$result = $conn->query($query);
	$row = $result->fetch(PDO::FETCH_ASSOC);
	$idPatient = $row['idPatient'];
	
	$str = "";
	$query = "SELECT COUNT(*) FROM surveys WHERE idPatient = $idPatient ORDER BY datesurvey DESC";
	if ($result = $conn->query($query)) {
		if ($result->fetchColumn() > 0) {
			$query = "SELECT COUNT(*) FROM surveys WHERE idPatient = $idPatient ORDER BY datesurvey DESC";
			$i = $conn->query($query)->fetchColumn();
			$query = "SELECT datesurvey, conclusion, diagnosis ";
			$query .= "FROM surveys ";
			$query .= "WHERE idPatient = $idPatient ";
			$query .= "ORDER BY datesurvey DESC ";
			
			$result = $conn->query($query);
			while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
				$str .= "<div class=\"user-survey-results\">";
				$str .= "	<p>Номер оследования $i</p>";
				$str .= '	<p>Дата обследования '.$row['datesurvey'].'</p>';
				$str .= '	<p>Название обследования</p>';
				$str .= '	<p>Заключение<br> ';
				$str .= $row['conclusion'].'</p>';
				$str .= '	<p>Диагноз<br> ';
				$str .= $row['diagnosis'].'</p>';
				$str .= '</div>';
				$i--;
			}	
		} else
			$str = "Вы ещё не проходили обследование";
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Личный кабинет - Персональные данные</title>
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
				<h1>Личный кабинет - Персональные данные</h1>
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
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
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Личный кабинет - Запись на прием к врачу</title>
		<meta charset="utf-8">
		<link rel="stylesheet" href="css/style-main.css">
		<link rel="stylesheet" href="css/style-content.css">
		<link rel="stylesheet" href="css/user-schedule.css">
<!--<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">-->
		<link rel="stylesheet" href="css/style-autocomplete.css">
		
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
		<script src="js/javascript.js"></script>			
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
				<h1>Личный кабинет - Запись на прием к врачу</h1>
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
				<div class="container">
					<form id="appoint-survey-form" action="user-appointments.php" method="post">
						<input id="fio-doctor" name="fio-doctor" type="text" placeholder="Введите ФИО врача"><br>
						<!--
						<div id="autocomplete">
							<ul class="ui-autocomplete">
								<li class="ui-menu-item" id="ui-id-5" tabindex="-1">Петров Петр Петрович</li>
								<li class="ui-menu-item" id="ui-id-6" tabindex="-1">Иванов Иван Иванович</li>
								<li class="ui-menu-item" id="ui-id-7" tabindex="-1">Сидоров Семен Семенович</li>
							</ul>
						</div>
						-->
						<p>Выберите дату</p>
						<input id="date" name="date" type="date"><br>
						<p>Выберите время начала приема</p>
						<input id="timeBegin" name="timeBegin" type="time"><br>
						<p>Выберите время окончания приема</p>
						<input id="timeEnd" name="timeEnd" type="time"><br>
						<input id="appoint-survey-button" type="button" value="Записаться">
					</form>
					<div>
						<p id="currentTime">Выберите время</p>
					</div>	
					<div id="user-schedule">
					</div>
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
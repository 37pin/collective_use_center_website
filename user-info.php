<?php 
	session_start();
	require 'php\connect.php';
	
	$idUser = $_SESSION['iduser'];
	if (!empty($idUser)) {
		$conn = connect::getConnection();
		$query = "SELECT type FROM users WHERE iduser = $idUser";
		$result = $conn->query($query);
		$row = $result->fetch(PDO::FETCH_ASSOC);
		$type = $row['type'];
		if ($type != "patient") {
			header('Location: index.php', true, 303);
			die();
		}
		$query = "SELECT * FROM users WHERE iduser = $idUser";
		$result = $conn->query($query);
		$row = $result->fetch(PDO::FETCH_ASSOC);
		$surname = $row['surname'];
		$name = $row['name'];
		$lastname = $row['lastname'];
		$gender = strtoupper($row['gender']);
		$gender = $gender == "M" ? "Мужской" : "Женский";
		$email = $row['email'];
		$tel = $row['tel'];
		
		$query = "SELECT address, birthday, CURDATE() AS cur, organization FROM patients WHERE iduser = $idUser";
		$result = $conn->query($query);
		$row = $result->fetch(PDO::FETCH_ASSOC);
		$address = $row['address'];
		$birthday = $row['birthday'];
		$currentDate = $row['cur'];	
		$year = countYears($birthday, $currentDate);
		$organization = $row['organization'];
	} else
		header('Location: signin.php', true, 303);
	
	function countYears($birthday, $currentDate) {
		list($yearB, $monthB, $dayB) = preg_split('/-/', $birthday);
		list($yearC, $monthC, $dayC) = preg_split('/-/', $currentDate);
		$year = $yearC - $yearB;
		
		if ($monthB > $monthC)
			return $year - 1;
		if ($dayB > $dayC)
			return $year - 1;
		return $year;
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
				<p>Фамилия: <?=$surname;?></p>
				<p>Имя: <?=$name;?></p>
				<p>Отчество: <?=$lastname;?></p>
				<p>Дата рождения: <?=$birthday;?></p>
				<p>Возраст: <?=$year;?></p>
				<p>Пол: <?=$gender;?></p>
				<p>Почта: <?=$email;?></p>
				<p>Адрес: <?=$address;?></p>
				<p>Телефон: <?=$tel;?></p>
				<p>Направление: <?=$organization;?></p>
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
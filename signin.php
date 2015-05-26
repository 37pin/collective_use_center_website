<?php
	session_start();
	require 'php\connect.php';
	require 'php\check.php';
	$message = '';
	if ($_GET['exit'] == true) {
		session_destroy();
	} else {
		$login = '';
		$password = '';

		$login = $_POST['login'];
		$password = $_POST['password'];
		
		if (!empty($login) || !empty($password)) {
			if (isset($_POST['login'])) {
				$login = checkLabel($login);
			}
			if (isset($_POST['password'])) {
				$password = checkLabel($password);
			}

			$login = strtolower($login);
			$conn = connect::getConnection();
			$query = "SELECT iduser, password FROM users WHERE LOWER(login) = '$login'";
			$result = $conn->query($query);
			$row = $result->fetch(PDO::FETCH_ASSOC);
			$iduser = $row['iduser'];
			if (!empty($iduser)) {
				$hash = $row['password'];
				if (crypt($password, $hash)) {
					$_SESSION['iduser'] = $iduser;
				}
			} else {
				session_destroy();
				$message = "Неверная комбинация логина и пароля.";
			}
		}

		$iduser = $_SESSION['iduser'];
		if (!empty($iduser)) {
			$conn = connect::getConnection();
			$query = "SELECT type FROM users WHERE iduser = $iduser";
			$result = $conn->query($query);
			$row = $result->fetch(PDO::FETCH_ASSOC);
			$type = $row['type'];
			if (!empty($type)) {
				if ($type === 'employee') {
					header('Location: employee-adding-patient.php', true, 303);
					die();
				} else if ($type === 'doctor') {
					header('Location: doctor-schedule.php', true, 303);
					die();
				} else if ($type === 'admin') {
					header('Location: administrator-adding.php', true, 303);
					die();
				} else {
					header('Location: user-info.php', true, 303);
					die();
				}
			}
		}
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<title>Авторизация</title>
		<meta charset="UTF-8">
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
				<h1>Авторизация</h1>
			</div>
			<div class="container">
				<form method="post" action="signin.php">
					<?=$message;?></br>
					<input name="login" type="test" placeholder="Введите логин"><br>
					<input name="password" type="password" placeholder="Введите пароль"><br>
					<input type="submit" value="Войти">
				</form>
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
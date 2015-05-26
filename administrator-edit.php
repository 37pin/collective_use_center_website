<?php
	session_start();
	require 'php\connect.php';
	require 'php\generator.php';
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
	if ($type != "admin") {
		header('Location: signin.php', true, 303);
		die();
	}

	$message = '';
	$message2 = '';

	if (isset($_POST['edit_admin_password'])) {
		$newPassword = '';
		$oldPassword = '';
		if (isset($_POST['new_password'])) {
			$newPassword = $_POST['new_password'];
			$newPassword = checkLabel($newPassword);
		}
		if (isset($_POST['old_password'])) {
			$oldPassword = $_POST['new_password'];
			$oldPassword = checkLabel($oldPassword);
		}
		if (strlen($newPassword) < 8) {
			$message = 'Пароль должен иметь длину не менее 8 символов.';
		} else {
			$query = "SELECT iduser, password FROM users WHERE LOWER(login) = 'admin'";
			$result = $conn->query($query);
			$row = $result->fetch(PDO::FETCH_ASSOC);
			$iduser = $row['iduser'];
			if (!empty($iduser)) {
				$hash = $row['password'];
				if (crypt($oldPassword, $hash)) {
					$newPassword = crypt($newPassword);
					$query = "UPDATE users SET password = '$newPassword' WHERE LOWER(login) = 'admin'";
					$result = $conn->exec($query);
					$message = 'Изменено.';
				}
			} else {
				$message = 'Текущий пароль не совпадает.';
			}
		}
	}

	if (isset($_POST['edit_passwords'])) {
		$newPassword = '';
		if (isset($_POST['new_password'])) {
			$newPassword = $_POST['new_password'];
			$newPassword = checkLabel($newPassword);
		}

		$iduser = '';
		if (isset($_POST['users'])) {
			$iduser = $_POST['users'];
			$iduser = checkLabel($iduser);
		}
		if (strlen($newPassword) < 8) {
			$message2 = 'Пароль должен иметь длину не менее 8 символов.';
		} else {
			if (!empty($iduser)) {
				$newPassword = crypt($newPassword);
				$query = "UPDATE users SET password = '$newPassword' WHERE iduser = '$iduser'";
				$result = $conn->exec($query);
				$message2 = 'Изменено.';
			} else {
				$message2 = 'Ошибка.';
			}
		}
	}

?>
<!DOCTYPE html>
<html>
	<head>
		<title>Личный кабинет - Изменение данных</title>
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
				<h1>Личный кабинет - Изменение данных</h1>
			</div>
			<nav class="experience">
				<li>
					<a href="administrator-adding.php">Добавить специалиста</a>
				</li>
				<li>
					<a href="administrator-edit.php">Изменение данных</a>
				</li>
				<li>
					<a href="signin.php?exit=true">Выйти</a>
				</li>
			</nav>
			<div class="main-section">
				<div class="container">
					<br>
					<form method="post" action="administrator-edit.php">
						Изменить пароль администратора<br>
						<input name="old_password" type="password" placeholder="Старый пароль" required><br>
						<input name="new_password" type="password" placeholder="Новый пароль" required><br>
						<input name="edit_admin_password" type="submit" value="Изменить">
						<br>
						<?php
							echo $message;
						?>
						<hr>
					</form>
					<form method="post" action="administrator-edit.php">
						Изменить пароль пользователя<br>
						<select name="users" size="1" required style="width: 300px;">
						<?php
							$query = "SELECT iduser, login, surname, name, lastname, type FROM users WHERE type != 'admin'";
							$result = $conn->query($query);
							$opt = ' selected';
							while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
								$iduser = $row['iduser'];
								$login = $row['login'];
								$surname = $row['surname'];
								$name = $row['name'];
								$lastname = $row['lastname'];
								$type = $row['type'];
								if ($type === 'employee') {
									$type = 'сотрудник';
								} else {
									if ($type === 'doctor') {
										$type = 'доктор';
									} else {
										$type = 'клиент';
									}
								}
								echo "<option$opt value=\"$iduser\">$login - $surname $name $lastname - $type";
								$opt = '';
    						}
						?>
						</select>
						<?php
						?>
						<br>
						<input name="new_password" type="password" placeholder="Новый пароль" required>
						<br>
						<input name="edit_passwords" type="submit" value="Изменить">
						<br>
						<?php
							echo $message2;
						?>
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
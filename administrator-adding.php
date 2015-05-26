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
	$surname = '';
	$name = '';
	$lastname = '';
	$tel = '';
	$email = '';
	$gender = '';
	$tp = '';
	$dolgn = '';
	$org = '';

	if (isset($_POST['surname'])) {
		$surname = $_POST['surname'];
		$surname = checkLabel($surname);
	}
	if (isset($_POST['name'])) {
		$name = $_POST['name'];
		$name = checkLabel($name);
	}
	if (isset($_POST['lastname'])) {
		$lastname = $_POST['lastname'];
		$lastname = checkLabel($lastname);
	}
	if (isset($_POST['tel'])) {
		$tel = $_POST['tel'];
		$tel = checkLabel($tel);
	}
	if (isset($_POST['email'])) {
		$email = $_POST['email'];
		$email = checkLabel($email);
	}
	if (isset($_POST['gender'])) {
		$gender = $_POST['gender'];
		$gender = checkLabel($gender);
	}
	if (isset($_POST['tp'])) {
		$tp = $_POST['tp'];
		$tp = checkLabel($tp);
	}
	if (isset($_POST['dolgn'])) {
		$dolgn = $_POST['dolgn'];
		$dolgn = checkLabel($dolgn);
	}
	if (isset($_POST['org'])) {
		$org = $_POST['org'];
		$org = checkLabel($org);
	}

?>
<!DOCTYPE html>
<html>
	<head>
		<title>Личный кабинет - Добавить специалиста</title>
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
				<h1>Личный кабинет - Добавить специалиста</h1>
			</div>
			<nav class="experience">
				<li>
					<a href="administrator-adding.php">Добавить специалиста</a>
				</li>
				<li>
					<a href="signin.php?exit=true">Выйти</a>
				</li>
			</nav>
			<div class="main-section">
				<div class="container">
					<?php 
					if (empty($surname) || empty($name) || empty($lastname) || empty($gender) || empty($tp)) {
						if (isset($_POST['tp'])) {
							echo 'Заполните необходимые поля.';
						}
					?><br>
					<form method="post" action="administrator-adding.php">
						<input name="surname" type="text" placeholder="Фамилия" required value="<?php echo $surname;?>"><br>
						<input name="name" type="text" placeholder="Имя" required value="<?php echo $name;?>"><br>
						<input name="lastname" type="text" placeholder="Отчество" value="<?php echo $lastname;?>"><br>
						<input name="tel" type="tel" placeholder="Телефон" value="<?php echo $tel;?>"><br>
						<input name="email" type="email" placeholder="E-mail" value="<?php echo $email;?>"><br>
						<div>
							Пол 
						<select name="gender">
					  		<option selected value="M">М</option>
					  		<option <?php echo $gender === 'F' ? 'selected' : ''; ?> value="F">Ж</option>
					  	</select><br>
							<input name="tp" type="radio" value="doctor" checked>Врач<br>
							<input name="dolgn" type="text" placeholder="Должность врача"><br>
							<input name="org" type="text" placeholder="Организация врача">
							<input name="tp" type="radio" value="employee" <?php echo $tp === 'employee' ? 'checked' : ''; ?>>Сотрудник<br>
						</div>
						<input type="submit" value="Добавить">
					</form>
					<?php
					} else {
						$conn = connect::getConnection();
						$password = genPassword(8);
						$password = 'employee'; #УДАЛИТЬ

						$login = translit(iconv_substr($name, 0, 1, 'UTF-8')).'.'.translit($surname);
						$query = 'SELECT MAX(iduser) AS id FROM users';
						$result = $conn->query($query);
						$row = $result->fetch(PDO::FETCH_ASSOC);
						$id = $row['id'];
						if (!empty($id)) {
							$login .= $id + 1;
				    }

				    $login = strtolower($login);
				    $query = "SELECT COUNT(login) AS num FROM users WHERE login = '$login'";
				    $result = $conn->query($query);
				    $row = $result->fetch(PDO::FETCH_ASSOC);
				    $num = $row['num'];
				    while($num != 0) {
				    	$login .= rand(0, 99);
				    	$query = "SELECT COUNT(login) AS num FROM users WHERE login = '$login'";
					    $result = $conn->query($query);
					    $row = $result->fetch(PDO::FETCH_ASSOC);
					    $num = $row['num'];
				    }

				    $pass = crypt($password);
				    $query = "INSERT INTO users(login, password, email, tel, surname, name, lastname, gender, type) VALUES ('$login', '$pass', '$email', '$tel', '$surname', '$name', '$lastname', '$gender', '$tp')";
						$result = $conn->exec($query);
						if ($tp == 'doctor') {
							$query = 'SELECT MAX(iduser) AS id FROM users';
							$result = $conn->query($query);
							$row = $result->fetch(PDO::FETCH_ASSOC);
							$id = $row['id'];
							$query = "INSERT INTO doctors(iduser, post, organization) VALUES ($id, '$dolgn', '$org')";
							$result = $conn->prepare($query);
							$result->execute();
						}
						echo 'Логин: '.$login.'<br>Пароль: '.$password;
						}
					?>
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
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
	if ($type != "employee") {
		header('Location: index.php', true, 303);
		die();
	}
	
	$query = "SELECT idcategory, category FROM categories";
	$category = "<select name=\"category\" size=\"1\">";
	$result = $conn->query($query);
	while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
		$category .= '<option value="'.$row['idcategory'].'">'.$row['category'].'</option>';
	}
	$category .= '</select>';
	
	if (isset($_POST['addPatient'])) {
		$surname = $_POST['surname'];
		$name = $_POST['name'];
		$lastname = $_POST['lastname'];
		$birthday = $_POST['birthday'];
		$gender = $_POST['gender'];
		$email = $_POST['email'];
		$phone= $_POST['phone'];
		$address = $_POST['address'];
		$info = $_POST['info'];
		$idCategory = $_POST['category'];
		$surname = checkLabel($surname);
		$name = checkLabel($name);
		$lastname = checkLabel($lastname);
		$birthday = checkLabel($birthday);
		$gender = checkLabel($gender);
		$email = checkLabel($email);
		$phone = checkLabel($phone);
		$address = checkLabel($address);
		$info = checkLabel($info);
		$idCategory = checkLabel($idCategory);		
		
		if (!empty($surname) && !empty($name) && !empty($lastname) && !empty($birthday) && !empty($gender) && !empty($email) && !empty($phone) && !empty($address) && !empty($info) && !empty($category)) {
			$password = genPassword(8);
			//$password = crypt('user'); #УДАЛИТЬ
			$login = translit(iconv_substr($name, 0, 1, 'UTF-8')).'.'.translit($surname);
			$login = strtolower($login);
			
			$query = "INSERT INTO users ";
			$query .= "(login, password, email, tel, surname, name, lastname, gender, type)";
			$query .= "VALUES ";
			$query .= "('$login', '$password', '$email', '$phone', '$surname', '$name', '$lastname', '$gender', 'patient')";
			$result = $conn->prepare($query);
			$result->execute();
			
			$query = "SELECT idUser FROM users WHERE surname='$surname' AND name='$name' AND lastname='$lastname'";
			$result = $conn->query($query);
			$row = $result->fetch(PDO::FETCH_ASSOC);
			$patientIdUser = $row['idUser'];
			
			$query = "INSERT INTO patients";
			$query .= "(idUser, address, telephone, organization, birthday, idCategory) ";
			$query .= "VALUES ";
			$query .= "($patientIdUser, '$address', '$phone', '$info', '$birthday', $idCategory)";
			$result = $conn->prepare($query);
			$result->execute();
			
			$str = 'Логин: '.$login.'<br>Пароль: '.$password;
		}
	}

?>
<!DOCTYPE html>
<html>
	<head>
		<title>Личный кабинет - Добавить пациента</title>
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
				<h1>Личный кабинет - Добавить пациента</h1>
			</div>
			<nav class="experience">
				<li>
					<a href="employee-adding-patient.php">Добавить пациента</a>
				</li>
				<li>
					<a href="employee-adding-result-survey.php">Добавить результаты обследования</a>
				</li>
				<li>
					<a href="php\exit.php">Выход</a>
				</li>
			</nav>
			<div class="main-section">
				<div class="container">
					<?
						if (empty($str)) {
					?>
					<form method="post" action="employee-adding-patient.php">
						<input name="surname" type="text" placeholder="Введите фамилию"><br>
						<input name="name" type="text" placeholder="Введите Имя"><br>
						<input name="lastname" type="text" placeholder="Введите Отчество"><br>
						<p>Дата рождения</p>
						<input name="birthday" type="date"><br>
						<p>Пол</p>
						<input name="gender" type="radio" value="M">Мужской<br>
						<input name="gender" type="radio" value="W">Женский<br>
						<input name="email" type="email" placeholder="Введите e-mail"><br>
						<input name="phone" type="text" placeholder="Введите телефон"><br>
						<input name="address" type="text" placeholder="Введите адрес"><br>
						<textarea name="info" placeholder="Введите направление"></textarea><br>
						<?=$category;?><br>
						<input name="addPatient" type="submit" value="Добавить">
					</form>
					<?
						} else 
							echo $str;
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
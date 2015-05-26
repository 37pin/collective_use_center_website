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
	
	if (isset($_POST['edit'])) {
		$diagnosis = $_POST['diagnosis'];
		$idsurvey = $_POST['idsurvey'];
		$diagnosis = checkLabel($diagnosis);
		$idsurvey = checkLabel($idsurvey);
		if (!empty($diagnosis) && !empty($idsurvey)) {
			$query = "UPDATE surveys SET diagnosis = '$diagnosis' WHERE idsurvey = $idsurvey";
			$result = $conn->exec($query);
		}
	}
	
	$str = "";
	$query = "SELECT COUNT(*) FROM surveys WHERE idDoctor = $idDoctor";
	if ($result = $conn->query($query)) {
		if ($result->fetchColumn() > 0) {
			$query = 'SELECT idsurvey, surname, name, lastname, datesurvey, conclusion, diagnosis ';
			$query .= 'FROM surveys s, users u, patients p ';
			$query .= "WHERE idDoctor = $idDoctor ";
			$query .= 'AND s.idpatient = p.idpatient ';
			$query .= 'AND p.iduser = u.iduser ';
			$query .= 'ORDER BY datesurvey DESC';
			$result = $conn->query($query);

			while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
				$idsurvey = $row['idsurvey'];
				$str .= '<form method="post">';
				$str .= '<div class="user-survey-results">';
				$str .= "<input name=\"idsurvey\" type=\"hidden\" value=\"$idsurvey\">";
				$str .= '	<p>Пациент: '.$row['surname'].' '.$row['name'].' '.$row['lastname'].'</p>';
				$str .= '	<p>Дата обследования: '.$row['datesurvey'].'</p>';
				$str .= '	<p>Заключение:<br> ';
				$conclusion = $row['conclusion'];
				$str .= empty($conclusion) ? "Отсутствует" : $conclusion.'</p>';
				$str .= '	<p>Диагноз:</p>';
				$str .= ' <textarea name="diagnosis" required>'.$row['diagnosis'].'</textarea><br>';
				$str .= '<input name="edit" type="submit" value="Изменить диагноз">';
				$str .= '</div>';
				$str .= '</form>';
			}
		} else
			$str = 'Вы не провели нe одного обследования';
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Личный кабинет - Просмотр истории пациента</title>
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
				<h1>Личный кабинет - Просмотр истории пациента</h1>
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
<?php
	require 'connect.php';
	$conn = connect::getConnection();
	
	$fio = $_POST['fio'];
	list($surname, $name, $lastname) = preg_split("/ /", $fio);
	$query = "SELECT idUser";
	$query .= " FROM users";
	$query .= " WHERE surname = '$surname'";
	$query .= " AND name = '$name'";
	$query .= " AND lastname = '$lastname'";
	$result = $conn->query($query);
	$row = $result->fetch(PDO::FETCH_ASSOC);
	$idUser = $row['idUser'];

	if (empty($idUser))
		die();
	
	$query = "SELECT idDoctor FROM doctors WHERE idUser = $idUser";
	$result = $conn->query($query);
	$row = $result->fetch(PDO::FETCH_ASSOC);
	$idDoctor = $row['idDoctor'];
	
	if (empty($idDoctor))
		die();
	
	$query = "SELECT daydate, time_begin, time_end";
	$query .= " FROM docschedule";
	$query .= " WHERE iddoctor = $idDoctor";
	$query .= " ORDER BY daydate";
	$query .= " LIMIT 7";

	$result = $conn->query($query);
	$ans = '';
	$translate = array(
		"Mon" => "Пн",
		"Tue" => "Вт",
		"Wed" => "Ср",
		"Thu" => "Чт",
		"Fri" => "Пт",
		"Sat" => "Сб",
		"Sun" => "Вс",
	);
	while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
		$dayDate = $row['daydate'];
		
		$timeBegin = toMinute($row['time_begin']);
		$timeEnd = toMinute($row['time_end']);
		
		$ans .= '<div class="day">';
		$ans .= '<div class="day-title" data-day-of-week="'.$dayDate.'">';

		$day = strftime("%a", strtotime($dayDate));
		$day = $translate[$day];
		$ans .= '<p>'.$day.'</p>';
		$ans .= '<p>'.strftime("%d.%m.%Y", strtotime($dayDate)).'</p>';
		$ans .= '</div>';
		$ans .= '<div class="day-time" data-options=\'{ ';
		$cnt = 0;
		$query = "SELECT time_begin, time_end FROM surschedule WHERE iddoctor = $idDoctor AND date_survey = '$dayDate' ORDER BY time_begin";

		$result2 = $conn->query($query);
		if ($result2->fetchColumn() > 0) {
			$result2 = $conn->query($query);
			$style = ' style="background:linear-gradient(90deg, ';
			while ($row2 = $result2->fetch(PDO::FETCH_ASSOC)) {
				$timeBegin2 = toMinute($row2['time_begin']);
				$timeEnd2 = toMinute($row2['time_end']);
				
				$cnt++;
				$ans .= '"'.$cnt.'":{"timeBegin": '.$timeBegin2.', "timeEnd": '.$timeEnd2.'},';
				if ($cnt == 1) {
					$style .= '#FF0000 '.$timeBegin.'px, ';
					$style .= '#00FF00 '.$timeBegin.'px, ';
				}
				$style .= '#00FF00 '.$timeBegin2.'px, ';
				$style .= '#FF0000 '.$timeBegin2.'px, ';
				$style .= '#FF0000 '.$timeEnd2.'px, ';
				$style .= '#00FF00 '.$timeEnd2.'px, ';
			}
			$style .= '#00FF00 '.$timeEnd.'px, ';
			$style .= '#FF0000 '.$timeEnd.'px);" ';
			$ans .= '"'.++$cnt.'":{"timeBegin": 0, "timeEnd": '.$timeBegin.'},';
			$ans .= '"'.++$cnt.'":{"timeBegin": '.$timeEnd.', "timeEnd": 600},';
			$ans = substr($ans, 0, strlen($ans) - 1);
			$ans .= ' }\''.$style.'></div>';
			$ans .= '</div>';
		} else {
			$ans .= '"1":{"timeBegin": 0, "timeEnd": '.$timeBegin.'},';
			$ans .= '"2":{"timeBegin": '.$timeEnd.', "timeEnd": 600}';
			$ans .= ' }\' style="background:linear-gradient(90deg, #FF0000 '.$timeBegin.'px,
				#00FF00 '.$timeBegin.'px, #00FF00 '.$timeEnd.'px,
				#FF0000 '.$timeEnd.'px);"></div>';
			$ans .= '</div>';
		}
	}
	if ($ans == "")
		die("Врач $surname $name $lastname ещё не создал расписание");
	echo $ans;
	
	function toMinute($time) {
		list($hour, $minute, $seconds) = preg_split("/:/", $time);
		return $hour * 60 + $minute - 480;
	}
?>
<?php
	require 'connect.php';
	$conn = connect::getConnection();
	
	$fio = $_GET['term'];
	$query = "SELECT COUNT(fio)"
		. " FROM (SELECT CONCAT( surname, ' ', name, ' ', lastname ) fio FROM users) AS T"
		. " WHERE fio LIKE '%$fio%' LIMIT 3";

	if ($result = $conn->query($query)) {
		if ($result->fetchColumn() > 0) {
			$query = "SELECT fio"
				. " FROM (SELECT CONCAT( surname, ' ', name, ' ', lastname ) fio FROM users) AS T"
				. " WHERE fio LIKE '%$fio%' LIMIT 3";
			$result = $conn->query($query);
			$str = "[";
			while($row = $result->fetch(PDO::FETCH_ASSOC)) {
				$str .= "\"".$row["fio"]."\", ";
			}
			$str = substr($str, 0, strlen($str) - 2);
			$str .= "]";
			echo $str;
		}
	} else
		die();
?>
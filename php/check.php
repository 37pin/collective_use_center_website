<?php
function checkLabel($str) {
    $str = stripslashes($str);
    $str = htmlspecialchars($str);
    $str = mysql_real_escape_string($str);
    $str = trim($str);
    return $str;
}
?>
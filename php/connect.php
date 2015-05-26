<?php
class Connect {
    static $connect;

    static function getConnection() {
        if (self::$connect === null) {
            try {
                self::$connect = new PDO('mysql:host=localhost;dbname=meddb','root','');
                self::$connect->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
            }
            catch (PDOException $e) {
                echo 'Connection Error: '.$e->getMessage();
            }
        }
        return self::$connect;
    }
}
?>
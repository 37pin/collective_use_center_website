<?php
    require_once 'phpunit.phar/Framework.php';
    require_once 'connect.php';

    class superTest extends PHPUnit_Framework_TestCase
    {

        public function testConnect()
        {
            $this->assertNotEquals(null, Connect::getConnection());
        }

    }
?>
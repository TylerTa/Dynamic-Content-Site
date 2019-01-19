<?php
/**
 * Created by PhpStorm.
 * User: Tyler
 * Date: 3/8/2017
 * Time: 12:03 AM
 */

session_start();
session_unset();
session_destroy();
header("Location: confirm02.php?state=1");

?>
<?php
/**
 * Created by PhpStorm.
 * User: Tyler
 * Date: 3/7/2017
 * Time: 10:42 AM
 */

/* CREATE A CONNECTION TO THE SERVER */
$dsn = '';
$user = '';
$pwd = '';

try
{
    $pdo = new PDO($dsn, $user, $pwd);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch (PDOException $e)
{
    echo "Error connecting to database!" . $e->getMessage();
    exit();
}

<?php
/**
 * Created by PhpStorm.
 * User: Tyler
 * Date: 4/16/2017
 * Time: 3:44 AM
 *
 * Comment: This version to download csv file is unless we have 'Write Permission' on the server.
 */


/* CREATE A CONNECTION TO THE SERVER */

//include "connect02.php";


$dsn = 'mysql:host=localhost;dbname=cs409lhta';
$user = 'cs409lhta';
$pwd = 'csci409sp17!';



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


/**
 * Once the user submit/click the 'Download CSV' button: process the CSV download
 */
if(isset($_POST['export_email_csv']))
{

    header('Content-Transfer-Encoding: ascii');
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=usersEmail.csv');

    /**
     * Create an output string: just like 'echo'
     *
     * method 1: fopen() - Opens file or URL: binds a named resource, specified by filename, to a stream
     *           php:// - Access various I/O streams
     *           php://output - Is a write-only stream that allows you to write to the output buffer mechanism in the same way as 'print' and 'echo'.
     */
    $output = fopen('php://output', 'w');    // fopen('php://output', 'w') - Is a function that open a file and within its parameter can write and read files.

    fputcsv($output, array('Email')); // This method formats a line (passed as a fields array) as CSV and write it.

    /**
     * Create a Query/SQL Statement that pulls all of the emails from the database
     */
    $query = "SELECT DISTINCT email FROM User ORDER BY id";
    //$row = mysqli_fetch_assoc($query);
    $stmtQuery = $pdo -> prepare($query);
    $stmtQuery -> execute();


    foreach ($stmtQuery as $row)
    {
        fputcsv($output, array($row['email'])); // Apparently you have to address the '$row' as an array itself with the and key values ['email']
    }


    fclose($output); // The fclose() function closes an open file.
}
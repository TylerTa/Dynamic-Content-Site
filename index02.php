<?php
/**
 * Created by PhpStorm.
 * User: Tyler
 * Date: 3/7/2017
 * Time: 10:06 AM
 */

$pageTitle = "Home Page";
$errormsg = "";
require_once "header02.php";
?>

<p><h5 class="w3-padding-32">Just a regular home page to show the functionality of the authentication system.</h5></p>

<?php
    if(isset($_SESSION['firstName'])) {echo "<div class='success'><p>Hello, " . $_SESSION['firstName'] . "! Welcome back!</p></div>";}

if(isset($_SESSION['userType'])) //This code was repeated a lot on each page...maybe i could include it into my header page?
{
    // Store the 'userType' into a variable
    // If 'userType' == 0 : (Regular User)
    // If 'userType' == 1 : (Admin User)
    $userType = $_SESSION['userType'];

    if($userType == 0)
    {
        echo "<div><p class='success'>You are currently logged in as a 'Regular' user type</p></div>";
    }
    else if($userType == 1)
    {
        echo "<div><p class='success'>You are currently logged in as an 'Admin' user type</p></div>";
    }


    echo "<a class=\"twitter-timeline\" data-width=\"400\" data-height=\"400\" href=\"https://twitter.com/KinjaDeals\">Tweets by KinjaDeals</a> <script async src=\"//platform.twitter.com/widgets.js\" charset=\"utf-8\"></script>";
}
else
{
    $errormsg .= "<p>Error: 'userType' was not set in the SESSION</p>";
}

    include_once "footer02.php";
?>



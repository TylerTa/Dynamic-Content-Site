<?php
    session_start();
    require_once "connect02.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title><?php echo $pagetitle; ?></title>
    <link rel="stylesheet" href="https://www.w3schools.com/lib/w3.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montserrat">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <link rel="stylesheet" href="styles02.css">

    <!-- TinyMCE (Advance Text Editor): Using Javascript -->
    <script src="https://cloud.tinymce.com/stable/tinymce.min.js?apiKey=rjxirgodtfvbb1s8zidvcks5wupytou4g5s6z18v66h5h63c"></script>
    <script>tinymce.init({ selector:'textarea' });</script>

    <!-- Google CDN (Content Delivery Network) for jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>

    <!-- This is the script tag that links to the 'jQueryDropDownContent02.js' required for the viewContent page to work -->
    <script type="text/javascript" src="jQueryDropDownContent02.js"></script>


</head>

<body>
    <!-- Navigation Bar -->
    <div class="navBar">
        <div class="w3-bar w3-red w3-card-2 w3-left-align w3-large">
            <a class="w3-bar-item w3-button w3-hide-medium w3-hide-large w3-opennav w3-right w3-padding-large w3-hover-white w3-large w3-red"
               href="javascript:void(0);" onclick="myFunction()" title="Toggle Navigation Menu"><i class="fa fa-bars"></i></a>

            <a href="index02.php" class="w3-bar-item w3-button w3-padding-large w3-white">Home</a>

            <a href="registration02.php" class="w3-bar-item w3-button w3-hide-small w3-padding-large w3-hover-white">Register</a>

            <?php
                echo (isset($_SESSION['id'])) ? "<a href='logout02.php' class='w3-bar-item w3-button w3-hide-small w3-padding-large w3-hover-white'>Log Out</a>" : "<a href='login02.php' class='w3-bar-item w3-button w3-hide-small w3-padding-large w3-hover-white'>Login</a>";

                echo "<a href='passwordReset02.php' class='w3-bar-item w3-button w3-hide-small w3-padding-large w3-hover-white'>Reset Password</a>";

                echo (isset($_SESSION['id'])) ? "<a href='addContent02.php' class='w3-bar-item w3-button w3-hide-small w3-padding-large w3-hover-white'>Add Content</a>" : null;

                echo (isset($_SESSION['id'])) ? "<a href='myContent02.php' class='w3-bar-item w3-button w3-hide-small w3-padding-large w3-hover-white'>My Content</a>" : null;

                echo "<a href='viewContent03.php' class='w3-bar-item w3-button w3-hide-small w3-padding-large w3-hover-white'>View Content</a>";

                echo (isset($_SESSION['id'])) ? "<a href='exportCSVFile02.php' class='w3-bar-item w3-button w3-hide-small w3-padding-large w3-hover-white'>CSV Download</a>" : null;

                if(isset($_SESSION['userType']))
                {
                    if ($_SESSION['userType'] == 1) {
                        //echo (isset($_SESSION['userType'])) ? "<a href='registeredUser02.php' class='w3-bar-item w3-button w3-hide-small w3-padding-large w3-hover-white'>Registered Users</a>" : null;

                        echo "<a href='registeredUser02.php' class='w3-bar-item w3-button w3-hide-small w3-padding-large w3-hover-white'>Registered Users</a>";
                    }
                }
            ?>

            <a href="retrieveUsername02.php" class="w3-bar-item w3-button w3-hide-small w3-padding-large w3-hover-white">Recover Username</a>
        </div>
    </div>

<?php
/**
 * Created by PhpStorm.
 * User: Tyler
 * Date: 3/7/2017
 * Time: 11:59 PM
 */

$pageTitle = "Confirmation";
require_once "header02.php";

if($_GET['state'] == 1) // logout02.php - header confirmation
{
    echo "<p>Logout confirmed. Please <a href='login02.php'>log in</a> again to view restricted content.</p>";
}
else if($_GET['state']==2) // login02.php - header confirmation
{
    echo "<div class='success'>
                <p>Thank you for logging in, " . $_SESSION['firstName'] . "!</p>
                <table>
                    <tr><th>User ID</th><td>" . $_SESSION['id'] . "</td></tr>
                    <tr><th>Username</th><td>" . $_SESSION['uname'] . "</td></tr>
                </table>
         </div>";
}
else if($_GET['state']==3) // This state is when you have successfully sent of a userName recovery email.
{
    echo "<div class='success'><p>Success: Your username was sent to your email</p></div>";
    //echo "<p>Success: Your username was sent to your email</p>";

    // Destroy/end the whole session since nobody is logged in nor could some logged in have a link to a username recovery page
    // - Good use for security reason???
    session_destroy();
}
else if ($_GET['state']==4)
{
    echo "<div class='error'><p>Error: The email was not sent for some reason!!!</p></div>";
}
else if($_GET['state']==5)
{
    echo "<p>Error: Security answer does not match. Please return <a href='index02.php'>Home</a> and try again</p>";
}
else if($_GET['state'] == 6)
{
    echo "<div class='success'><p>Success: Your content was updated. <br>Please go back to <a href='myContent02.php'>My Content</a> to check & make sure.</p></div>";
}
else if($_GET['state'] == 7)
{
    echo "<div class='success'><p>Success: Thank you for registering.</p></div>";
}
else if($_GET['state'] == 8) // When Deletion of content was a success
{
    echo "<div class='success'><p>Success: content was successfully deleted.</p></div>";
}
else if($_GET['state'] == 9)
{
    echo "<div class='success'><p>Success: User status was changed.</p></div>";
}
else if($_GET['state'] == 10)
{
    echo "<div class='success'><p>Success: Your password was changed.</p></div>";
}

else
{
    echo "<p>Please continue by choosing an item from the menu.</p>";
}

require_once "footer02.php";

?>
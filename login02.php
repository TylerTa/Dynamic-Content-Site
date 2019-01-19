<?php
/**
 * Created by PhpStorm.
 * User: Tyler
 * Date: 3/7/2017
 * Time: 4:52 PM
 */

$pagetitle = "Login Form";
$pageh1 = "Login";
$showForm = 1;
$errormsg = "";
require_once "header02.php";

//FIRST CHECK TO SEE IF THE USER IS ALREADY LOGGED IN
if(isset($_SESSION['userid']))
{
    echo "<p class='error'>You are already logged in.</p>";
    include_once "footer.php";
    exit(); //This will exit the current script
}

if(isset($_POST['submit']))
{
    //CLEANSE DATA THE SAME AS THE REGISTRATION PAGE
    $formfield['unameLogin'] = strtolower(trim($_POST['unameLogin']));
    $formfield['passwordLogin'] = trim($_POST['passwordLogin']);

    //CHECKING FOR EMPTY FIELDS THE SAME AS THE REGISTRATION PAGE
    if (empty($formfield['unameLogin'])) {$errormsg .= "<p>USERNAME IS MISSING.</p>";}
    if (empty($formfield['passwordLogin'])) {$errormsg .= "<p>PASSWORD IS MISSING.</p>";}

    //DISPLAY ERRORS OR MOVE INTO TRY/CATCH
    if($errormsg != "")
    {
        echo "<div class='error'><p>There are errors: <br> " . $errormsg . "</p></div>";
    }
    else
    {
        //GET THE USER DATA FROM THE DATABASE
        try
        {
            $sqlloggin = "SELECT * FROM User WHERE username = :uname";
            $slogin = $pdo->prepare($sqlloggin);
            $slogin->bindValue(':uname', $formfield['unameLogin']);
            $slogin->execute();
            $rowlogin = $slogin->fetch();
            $countlogin = $slogin->rowCount();

            //if query okay, see if there is a result
            if ($countlogin < 1)
            {
                echo "<p class='error'>Invalid username: This user cannot be found.</p>";
                //$errormsg .= "<p class='error'>This user cannot be found.</p>";
            }
            else
            {
                if(password_verify($formfield['passwordLogin'],$rowlogin['password_hash']))
                {
                    // Password is correct
                    // - Create SESSION[''] variables to store user current logged in user info
                    $_SESSION['id'] = $rowlogin['id'];
                    $_SESSION['firstName'] = $rowlogin['first_name'];
                    $_SESSION['uname'] = $rowlogin['username'];
                    $_SESSION['userType'] = $rowlogin['user_type'];
                    $showform = 0; //make login form disappear
                    header("Location: confirm02.php?state=2");
                }
                else
                {
                    echo "<p class='error'>The password for this user you have entered is not correct. Please try again.</p>";
                }
            }//username exists
        }//try
        catch (PDOException $e)
        {
            echo "Error fetching users: " . $e ->getMessage();
            exit();
        }
    }//else error message
}// if(isset($_POST['submit']))

if($showForm == 1) {
    ?>
<div class="w3-row-padding w3-light-grey w3-padding-64 w3-container">
    <p>Please enter your username and password to login</p>
    <form name="loginForm" id="loginForm" method="post" action="login02.php">
        <table>
            <tr>
                <td>Username:</td>
                <td><input type="text" name="unameLogin" id="uname" /></td>
            </tr>

            <tr>
                <td>Password:</td>
                <td><input type="password" name="passwordLogin" id="pwd" /></td>
            </tr>

            <tr>
                <td>Submit:</td>
                <td><input type="submit" name="submit" value="submit"/></td>
            </tr>
        </table>
    </form>
</div>
    <?php
} //$showForm
?>

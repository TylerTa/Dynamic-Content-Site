<?php
/**
 * Created by PhpStorm.
 * User: Tyler
 * Date: 3/8/2017
 * Time: 11:40 PM
 */

$pagetitle="Password Reset";
$pageh1 = "Password Reset:";
$errormsg = "";
$showPasswordResetForm = 1;

require_once "header02.php";

/**************************************************************************
 * Initialized Variables we need from the $_SESSION[''] Array Variables
 **************************************************************************/
$sessionUsername = $_SESSION['retrievedUsername'];
$sessionSubmitTimeStamp = $_SESSION['submitTimeStamp'];

/**
 * Compare the currentTimeStamp with the submitTimeStamp that was store in the database
 * - If the time is over 'x' seconds (In this case 60seconds) then the link have expired
 */
//  Using the 'username' taken from the previous browser passwordRecover.php through the $_SESSION['username'] Variables
//  - We want to use SELECT statement to pull data from the User with $sessionUsername
//  - The we definitely want to grab the recover_hash value from the database and compare it with the hash query hash from the URL (?q=*********)
try
{
    $sqlUser = "SELECT recover_hash FROM User WHERE username = :sessionUsername";
    $stmtUser = $pdo -> prepare($sqlUser);
    $stmtUser -> bindValue(':sessionUsername', $sessionUsername);
    $stmtUser -> execute();
    $userHash = $stmtUser -> fetch(); // Fetch the data array executed from $sqlUserStmt
    $rowCount = $stmtUser -> rowCount(); //Check to see if any row was pulled from the database.

    if($rowCount < 1)
    {
        echo "<div class='error'><p>Error: User hash was not fetched from the database  <br/>". $e->getMessage() ."</p></div>";
    }
    else
    {
        /****************************************
         * Grab/$_GET[''] the 'hash' from the URL query
         ****************************************/
        $urlHash = $_GET['q'];

        /********************************************************************************
         * Compare the hash for the URL Query with the hash SELECTED FROM the database
         * - use the strcmp() functioin to compare strings
         *******************************************************************************/
        $comparedHash = strcmp($userHash['recover_hash'], $urlHash);

        if($comparedHash == 0)
        {
            echo "<div class='success'><p>Success!!! The URL Hash matched the recover_hash from the database!!! :)</p></div>";

            /******************************************************************************************************
             * !!!Very F***ing IMPORTANT TO SET THE DEFAULT TIMEZONE TO EASTERN TIME OR ELSE IT WOULD BE GMT TIME!!!
             *****************************************************************************************************/
            date_default_timezone_set('US/Eastern');
            $currentTimeStamp = date('Y-m-d G:i:s'); //Using the date() function to grab the current timestamp

            // Convert $sessionSubmitTimeStamp to a Unix Time
            $unixSessionSubmitTimeStamp = strtotime($sessionSubmitTimeStamp);

            // Convert $currentTimeStamp to a Unix Time
            $unixCurrentTimeStamp = strtotime($currentTimeStamp);

            /********************************************
             * Used For Testing The TimeStamps
             ********************************************/


            /*****************************************************************************************************
             * Compare the currentTimeStamp with submitTimeStamp from the database to see if time has expired
             * - (In this case, our expired time is 60 seconds)
             * *****************************************************************************************************/
            if(($unixCurrentTimeStamp - $unixSessionSubmitTimeStamp) <= 60)
            {
                echo "<div class='success'><p>Valid Time: You made just in time before your reset password link expired</p></div>";
            }
            else // If the time has expired, then alert the user and hide the password reset form
            {
                echo "<div><p>It seem like your reset password link have expired, please try again. (for testing purpose the link will expired after 60 seconds)</p></div>";

                // Do not present the password reset form if the time have expired.
                $showPasswordResetForm = 0;
            }
        }
        else // The user 'hash' from the database did not matched the 'hash' from the URL
        {
            echo "<div class='error'><p>Error: The user 'hash' from the database did not matched the 'hash' from the URL</p></div>";

            // Hide the password reset form
            $showPasswordResetForm = 0;
        }

    }
}
catch(PDOException $e)
{
    echo "<div class='error'><p>ERROR: Trying to select the recover_hash from the database WHERE username = ". $_SESSION['retrievedUsername'] ." // ". $e->getMessage() ."</p></div>";
}

/***********************************************************************
 * If the user submit the new password from the reset password form
 **********************************************************************/
if(isset($_POST['submitPassReset']))
{
    /************************************************************************
     * CREATE VARIABLES TO STORE USER DATA INPUT FROM THE PASSWORD FORMFIELDS
     ************************************************************************/
    $updatedPassword = $_POST['newPasswordInput'];
    $confirmPassword = $_POST['confirmPasswordInput'];

    /***************************************************************************
     * Using 'Regular Expressions' to validate the requirement for the password
     ***************************************************************************/
    $uppercase = preg_match('@[A-Z]@', $updatedPassword);
    $lowercase = preg_match('@[a-z]@', $updatedPassword);
    $number    = preg_match('@[0-9]@', $updatedPassword);

    if(!$uppercase || !$lowercase || !$number || strlen($updatedPassword) < 8)
    {
        // Create an Error Message
        $errormsg .= "<p class='error'>Error: Your password is missing one or more of the requirements: <br/>" .
            "- Must be a minimum of 8 characters <br/>" .
            "- Must contain at least 1 number <br/>" .
            "- Must contain at least one uppercase character <br/>" .
            "- Must contain at least one lowercase character</p>" ;
    }

    /**
     * - If there are ERRORS: then display it to the screen
     * - Else: Continue to input and update the new password for the user
     */
    if($errormsg != "")
    {
        echo "<p class='error'>There are errors: ". $errormsg ."</p>";
    }
    else
    {
        // If $updatedPassword & $confirmPassword matched
        // - Proceed to update the user new password
        if($updatedPassword == $confirmPassword)
        {
            /******************************************************************************
             * Cleanse the password using the trim() function
             *****************************************************************************/
            $cleansedPassword = trim($updatedPassword);

            /***************************************************************************************
             * Hash The '$cleansedPassword' using 'better_crypt()' Blowfish encryption function
             *****************************************************************************************/
            $hashedPassword = password_hash($cleansedPassword, PASSWORD_BCRYPT);

            /********************************************************************************
             * CREATE A SQL STATE TO UPDATE/CHANGE THE OLD PASSWORD WITH THE UPDATED ONE
             ********************************************************************************/
            try
            {
                $sqlUpdatePass = "UPDATE User SET password_hash = :password_hash WHERE username = :sessionUsername;";
                $stmtUpdatePass = $pdo -> prepare($sqlUpdatePass);
                $stmtUpdatePass -> bindValue(':password_hash', $hashedPassword);
                $stmtUpdatePass -> bindValue(':sessionUsername', $sessionUsername);
                $stmtUpdatePass -> execute();

                // Hide the password reset form
                $showPasswordResetForm = 0;

                // Display a SUCCESS alert
                echo "<div class='success'><p>Success: There was no noticeble errors, Your password should be updated now. :) <br/>
                                          Please go to the login page to login with your new password.</p></div>";

                // We are going to end the session for security reason???
                session_destroy();

                // Should we also end the connection with the database???
                // - since we are not using it anymore???
                $pdo = null;

                header("Location: confirm02.php?state=10");
            }
            catch(PDOException $e)
            {
                echo "<div class='error'><p>Error: Was not able to UPDATE the old password with the updated one!!!" . $e->getMessage() . "</p></div>";
            }
        }
        else // The updated password and the confirm password does not matched
        {
            echo "<div class='error'><p>Error: The passwords entered do not match please try again. </p></div>";
        }
    }
}

if($showPasswordResetForm == 1)
{
    ?>

    <form name="resetPassForm" id="resetPassForm" method="post" action="resetPass02.php">
        <table>
            <tr>
                <th><label for="resetPassInput">New Password</label></th>
                <td><input type="password" name="newPasswordInput" id="newPasswordInput" required</td>
            </tr>

            <tr>
                <th><label for="confirmPassInput">Confirm Password</label></th>
                <td><input type="password" name="confirmPasswordInput" id="confirmPassInput" required</td>
            </tr>

            <tr>
                <th>Submit:</th>
                <td><input type="submit" name="submitPassReset" value="submitPassReset"/></td>
            </tr>
        </table>
    </form>

    <?php
}
else
{
    echo "<div class='error'><p>Error: Sorry, but you are not authorized yet to use this page.<br/>
                                Please come back when you have the correct unique recover link sent to you through email.</p>";
}

include_once "footer02.php";
?>
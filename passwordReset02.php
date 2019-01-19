<?php
/**
 * Created by PhpStorm.
 * User: Tyler
 * Date: 3/8/2017
 * Time: 8:56 PM
 */

$pagetitle="Password Reset";
$pageh1 = "Password Reset:";
$errormsg = "";
$showRecoverForm = 1;
$showQuestionForm = 0;

require_once "header02.php";

/**********************************************
 * If the user submit the 'resetPassForm'
 **********************************************/
if(isset($_POST['submitPassRecover']))
{
    /****************************************************************************
     * CREATE VARIABLES TO STORE USER DATA INPUT & SANITIZE DATA FROM USER.
     *****************************************************************************/
    $passResetForm['username'] = trim(strtolower($_POST['username']));
    $passResetForm['email'] = trim(strtolower($_POST['emailPassword']));

    /***************************************
     * CHECK FOR EMPTY FIELDS
     *****************************************/
    if(empty($passResetForm['username'])) {$errormsg .= "<p>The username field is empty</p>";}
    if(empty($passResetForm['email'])) {$errormsg .= "<p>The email field is empty</p>";}

    /******************************************************************************
     * - if THERE ARE ANY ERRORS: Display $errormsg
     * - else MOVE ONTO VALIDATING email & username with the database.
     *****************************************************************************/
    if($errormsg != "")
    {
        echo "<div class='error'><p>There are errors: <br>". $errormsg ."</p></div>";
    }
    else
    {
        /*************************************************************
         * CHECK DATABASE IF THERE IS A VALID EMAIL & USERNAME IN THE DATABASE
         ************************************************************************/
        try {
            /*Create a query string to grab from the database the
              - email, username, secure_question, & secure_answer
              - using the email and username the user had inputted*/
            $sqlRecover = "SELECT * FROM User WHERE (email = :emailInput AND username = :usernameInput)";
            $stmtRecover = $pdo->prepare($sqlRecover); // Convert the sql query string into an sql query statement
            $stmtRecover->bindValue(':emailInput', $passResetForm['email']);
            $stmtRecover->bindValue(':usernameInput', $passResetForm['username']);
            $stmtRecover->execute();
            $rowRecover = $stmtRecover->fetch(); // This fetches all the queried data from the database requested by the SQL statement '$sqlRecover'
            // - store the result into a variable '$rowRecover' which is a Array Variable
            $rowCount = $stmtRecover->rowCount(); // This grab the number of data row fetched from the database

            /* CHECK TO SEE THERE WAS ANY 'email' OR 'username' IN THE DATABASE*/
            // If $rowRecover['email'] is empty then it would be the value of false???
            // - so my putting a '!' not tag in front it would cause it to be true
            // - which then run the block of code below
            // - prompting the user that the email inputted was invalid.
            if (!$rowRecover['email']) {
                echo "<p class='error'>Error: Email could not be found in the database. </p>";
            } else if (!$rowRecover['username']) {
                echo "<p class='error'>Error: Username could not be found in the database. </p>";
            }

            // If both of the 'email' and 'username' on top passed
            // - then there must be data pulled from the database
            // - and assigned to the $rowRecover[''] array
            if ($rowCount >= 1) {
                echo "<p>You have entered in a valid email address & username, 
                         your security question form field should pop up. </p>";
                echo "<h2>Security Question:</h2>";
                echo "<p>" . $rowRecover['secure_question'] . "</p>";

                // FLIP THE SWITCH ON WHICH FORM IS SHOWN BELOW
                // - 'email' and 'username' form should be cleared
                // - security answer form should be the one shown
                $showRecoverForm = 0;
                $showQuestionForm = 1;

                // Try to store nessisary information from the database onto a Global Variable $_SESSION
                // - to be re-use later (When the page is refreshed onto the security question form)
                $_SESSION['retrievedEmail'] = $rowRecover['email'];
                $_SESSION['retrievedUsername'] = $rowRecover['username'];
                $_SESSION['retrievedAnswer'] = $rowRecover['secure_answer'];
                $_SESSION['retrievedSalt'] = $rowRecover['salt'];
            }

        }
        catch(PDOException $e)
        {
            echo "<div class='error'><p>ERROR selecting 'email' & 'username' from database for valid email and username check!" . $e->getMessage() . "</p></div>";
        }

    }
} // if(isset($_POST['submitPassRecover']))
else if(isset($_POST['submitSecureAnswer'])) // If the user submit the security question form
{
    /***********************************
     * CHECK FOR EMPTY FORM FIELDS
     **********************************/
    if(empty($_POST['securityAnswer']))
    {
        $errormsg .= "Error: Security Answer field form was empty, please try again.";
    }

    /*****************************************************************************************
     * - if there are errors echo it to the screen
     * - else continue to submit the security answer
     ****************************************************************************************/
    if($errormsg != "")
    {
        echo "<div class='error'><p>THERE ARE ERRORS! <br/></p>";
        echo $errormsg;
        echo "</div>";
    }
    else
    {
        // Create a variable to store the security answer the user input into the security answer form
        // - remember to clense the data using trim()
        $securityAnswerInput = trim($_POST['securityAnswer']);

        // Initialize variables to be use and compare from the $_SESSION[''] array variables
        // - $_SESSION[''] should currently hold the data from previous extraction/fetching/sql SELECTION
        //   from if(isset($_POST['submitPassRecover']))
        // - Remember to cleanse the data using trim() & strtolower()
        $sessionUsername = $_SESSION['retrievedUsername'];
        $sessionEmail = $_SESSION['retrievedEmail'];
        $sessionAnswer = $_SESSION['retrievedAnswer'];
        $sessionSalt = $_SESSION['retrievedSalt'];

        // Use strcmp() function to compare strings: return a value of '0' if both string matched
        // - assign the value to a variable called $matchedAnswer
        // - variable $matchedAnswer value should be '0' if the two string was a matched
        $matchedAnswer = strcmp($securityAnswerInput, $sessionAnswer);

        // If $matchedAnswer == 0 then the user answer input & the database security_answer are the same
        if($matchedAnswer == 0)
        {
            // Generate the temporary unique hash to apply onto the end of reset.php? URL
            // - Using the retrieved 'salt' and 'email' we stored into the $_SESSION[''] Array Variable
            // - (we can retrieve it later with the $_GET[''] Global Array Variable)
            $recoverHash = hash('sha384', $sessionSalt . $sessionEmail);

            /****************************************************************************
             * Store the unique hash into the User database using an UPDATE statement
             * - Also try to update the current timestamp of when user answer the correct security_question
             ****************************************************************************/
            try
            {
                $sqlUpdateHashAndTime = "UPDATE User SET recover_hash = :recoverHash, expiredTimeStamp = now() WHERE email = :sessionEmail;";
                $stmtUpdate = $pdo -> prepare($sqlUpdateHashAndTime);
                $stmtUpdate -> bindValue(':recoverHash', $recoverHash);
                $stmtUpdate -> bindValue(':sessionEmail', $sessionEmail);
                $stmtUpdate -> execute();

                echo "<div class='success'><p>A unique password reset link has been emailed to your email address, You have 60 seconds to click the link before time expired.</p></div>";
                echo "<div class='success'><p>There are no errors. Your hash & expiredTimeStamp has been UPDATED/inserted to the database.</p></div>";
            }
            catch(PDOException $e)
            {
                echo "<div class='error'><p>Error: There was an error when trying to UPDATE the recover_hash in the database.". $e->getMessage() ."</p></div>";
            }

            /******************************************************************************************************************************
             * Create another SQL query statement to store the 'expiredTimeStamp into a $SESSION[''] Array Variable
             * - Used to compare the currentTimeStamp with the expiredTimeStamp from the database to calculate if the time has expired
             *******************************************************************************************************************************/
            //Make another try/catch sql statement request for the expiredTimeStamp
            try
            {
                $sqlTimeStamp = "SELECT expiredTimeStamp FROM User WHERE email = :sessionEmail";
                $stmtTimeStamp = $pdo -> prepare($sqlTimeStamp);
                $stmtTimeStamp -> bindValue(':sessionEmail', $sessionEmail);
                $stmtTimeStamp -> execute();
                $timeStampRow = $stmtTimeStamp -> fetch();
                $timeStampCount = $stmtTimeStamp -> rowCount();

                if($timeStampCount < 1)
                {
                    echo "<h2>Error: There were not any row pulled from the database for a timeStamp</h2>";
                }
                else
                {
                    $_SESSION['submitTimeStamp'] = $timeStampRow['expiredTimeStamp'];
                }
            }
            catch(PDOException $e)
            {
                echo "<div class='error'><p>Error: There was an error when trying to SELECT the expiredTimeStamp in the database. ". $e->getMessage() ."</p></div>";
            }

            /*********************************************************************
             * Create a URL String with the attached $recoverHash as the query
             * - which we will direct the user to reset their password
             * - We will retrieved the $recoverHash from the URL using a $_GET['']
             *********************************************************************/
            $resetURL = "ccuresearch.coastal.edu/lhta/csci409sp17/dynamicContentSite/resetPass02.php?q=". $recoverHash;

            /****************************************************
             * CREATE VARIABLES TO HOLD YOUR EMAIL PARAMETERS
             ****************************************************/
            $to = $sessionEmail;
            $subject = "Password Reset";
            $message = "This is your unique link to reset your password. You have 60 seconds before the link expire. <br> " . $resetURL;
            $headers = "From: passwordReset02.php";

            /***********************************************************************************
             * USING THE MAIL FUNCTION TO SEND THE EMAIL CONTAINING PASSWORD RESET URL LINK
             ***********************************************************************************/
            if(mail($to, $subject, $message, $headers))
            {
                echo "<div class='success'><p>SUCCESS: Password Reset Email Sent!!!</p></div>";

                //Do not show the either the Email or Question form if the email was sent
                $showQuestionForm = 0;
                $showRecoverForm = 0;
            }
            else
            {
                echo "<div class='error'><p>ERROR: Password Reset Email Was Not Sent!!!</p></div>";
            }
        }
        else // The security answer did not matched
        {
            echo "<div class='error'><p>ERROR: THE SECURITY ANSWERS DOESN'T MATCHED!!!</p></div>";

            session_destroy();
        }

    }

}


if($showRecoverForm == 1)
{
    ?>
    <form name="resetPassForm" id="resetPassForm" method="post" action="passwordReset02.php">
        <table>
            <tr>
                <th><label for="username">Username</label></th>
                <td><input type="text" name="username" id="username" required/></td>
            </tr>

            <tr>
                <th><label for="emailPassword">Email</label></th>
                <td><input type="email" name="emailPassword" id="emailPassword" required/></td>
            </tr>

            <tr>
                <th>Submit:</th>
                <td><input type="submit" name="submitPassRecover" value="submit"/></td>
            </tr>
        </table>
    </form>

    <?php
}
else if($showQuestionForm == 1)
{
    ?>
    <form name="secureQuestionForm" id="secureQuestionForm" method="post" action="passwordReset02.php">
        <table>
            <tr>
                <th><label for="securityAnswer">Security Answer:</label></th>
                <td><input type="text" name="securityAnswer" id="securityAnswer"</td>
            </tr>

            <tr>
                <th>Submit:</th>
                <td><input type="submit" name="submitSecureAnswer" id="submitSecureAnswer"</td>
            </tr>
        </table>
    </form>
    <?php
}

include_once "footer02.php";
?>
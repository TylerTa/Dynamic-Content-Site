<?php
/**
 * Created by PhpStorm.
 * User: Tyler
 * Date: 3/8/2017
 * Time: 12:22 AM
 */

$pagetitle="Username Retrieval 02";
$pageh1 = "Username Retrieval 02:";
$errormsg = "";
$showQuestionForm = 0;
$showEmailForm = 1;

require_once "header02.php";

if(isset($_POST['submit']))
{
    /****************************************************************************
     * CREATE VARIABLES TO STORE USER DATA INPUT & SANITIZE DATA FROM USER.
     *****************************************************************************/
    $emailForm['retrieveEmail'] = trim(strtolower($_POST['retrieveEmail']));

    /***************************************
     * CHECK FOR EMPTY FIELDS
     *****************************************/
    if(empty($emailForm['retrieveEmail'])) {$errormsg .= "<p class='error'>The email form field is empty.</p>";}

    /********************************************************************************************
     * IF THERE ARE ANY $errormsg DISPLAY THEM OR ELSE MOVE INTO try/catch
     *******************************************************************************************/
    if($errormsg != "")
    {
        echo "<div class='error'><p>There are errors: <br> " . $errormsg . "</p></div>";
    }
    else
    {
        /*************************************************************
         * CHECK DATABASE IF THERE IS A VALID EMAIL IN THE DATABASE
         ************************************************************************/
        try
        {
            // Create a string query to grab from the database the email the user has inputted.
            $sqlEmail = "SELECT * FROM User WHERE email = :retrieveEmail";
            $stmtEmail = $pdo -> prepare($sqlEmail);
            $stmtEmail -> bindValue(':retrieveEmail', $emailForm['retrieveEmail']);
            $stmtEmail -> execute();
            $rowEmail = $stmtEmail -> fetch();
            $countRowEmail = $stmtEmail -> rowCount();

            // If an email was not found in the database
            // - Display an $errormsg
            // Else
            if($countRowEmail < 1) // If the sql statement could not fetch any data (there isn't an email in the database that the user inputted), then the row would be 0
            {
                $errormsg .= "<p class='error'>Invalid Email: Could not find email in the database</p>";
            }
            else
            {
                echo "<p>You have entered in a valid email address, 
                         your security question form field should pop up. </p>";
                echo "<h2>Security Question:</h2>";
                echo "<p>" . $rowEmail['secure_question'] . "</p>";

                //version 2: Try to store the information to be re-use in a Global SESSION
                $_SESSION['retrievedEmail'] = $rowEmail['email'];
                $_SESSION['retrievedUsername'] = $rowEmail['username'];
                $_SESSION['retrievedAnswer'] = $rowEmail['secure_answer'];

                // Switch the form value so that the Security Question form would appear after the user submit the correct email
                $showEmailForm = 0;
                $showQuestionForm = 1;
            }
        }
        catch(PDOException $e)
        {
            echo "<div class='error'><p>ERROR selecting email from database for valid email check!" . $e->getMessage() . "</p></div>";
        }
    }

}//if(isset($_POST['submit']))
else if(isset($_POST['submitAnswer']))
{
    // Solution: Set and use global $_SESSION[' '] array variables

    // 1.) CHECK TO SEE IF THE SECURITY ANSWER FORM FIELD IS EMPTY
    if(empty($_POST['securityAnswer'])) {$errormsg .= "<p class='error'>The security answer field form is empty, please try again.</p>";}

    // 2.) IF THERE IS AN ERROR: DISPLAY $errormsg, ELSE: MOVE ONTO PREPARING TO SENT USERNAME RECOVERY EMAIL
    if($errormsg != "")
    {
        echo "<div><p>There are errors: ". $e->getMessage() ."</p></div>";
    }
    else
    {
        // 2a.) CLEANSE USER DATA: The user input security answer from the form
        $userAnswerForm = trim($_POST['securityAnswer']);

        // 2b.) Compare the user input security answer with the secure_answer from the database
        //      - Use the data we pulled from the database and stored into the GLOBAL $_SESSION[''] variable array

        // Initialized seperate variables from the SESSION[''] variable array
        $retrievedAnswer = trim($_SESSION['retrievedAnswer']);
        $retrievedEmail =  trim(strtolower($_SESSION['retrievedEmail']));
        $retrievedUsername = $_SESSION['retrievedUsername'];

        // Use strcmp() function to compare the two string ($userAnswerForm, $retrievedAnswer): return a value of '0' if both string matched
        // - assign the value to a variable called $matchedAnswer
        // - variable $matchedAnswer value should be '0' if the two string was a matched
        $matchedAnswer = strcmp($userAnswerForm, $retrievedAnswer);

        // 2c.) If the securityAnswer matched then process with sending a userName recovery email
        if($matchedAnswer == 0)
        {
            // CREATE VARIABLES TO HOLD YOUR EMAIL PARAMETERS
            $to = $retrievedEmail;
            $subject = "Username Recovery";
            $message = "Your Username: ". $retrievedUsername . "";
            $headers = "From: retrieveUsername02.php";

            //4.)  USING THE MAIL FUNCTION TO SEND THE EMAIL CONTAINING THE USERNAME
            //     - Use an 'if' statement to see if the mail() function return true
            if(mail($to, $subject, $message, $headers))
            {
                // Or Maybe we should try to send the user to the confirm02.php for the success confirmation that the email was sent.
                // - Instead of using the commented out code below to display the success confirmation here
                header("Location: confirm02.php?state=3");

            }
            else // If the mail() function wasn't able to send off the email due to errors
            {
                header("Location: confirm02.php?state=4");
            }
        }
        else //If the security answers does NOT matched: Then we send them to the confirm02.php page with the '?state=3' as a $_GET['state'] variable in the url
        {
            header("Location: confirm02.php?state=5");
        }

    }
}

if($showEmailForm == 1)
{
    ?>

    <p>Please enter in a valid email.</p>
    <form name="retrievalForm" id="retrievalForm" method="post" action="retrieveUsername02.php">
        <table>
            <tr>
                <th><label for="retrieveEmail">Email:</label></th>
                <td><input type="email" name="retrieveEmail" id="retrieveEmail" required/></td>
            </tr>

            <tr>
                <th>Submit:</th>
                <td><input type="submit" name="submit" value="submit"/></td>
            </tr>
        </table>
    </form>

    <?php
}
else if($showQuestionForm == 1)
{
    ?>
    <form name="answerForm" id="answerForm" method="post" action="retrieveUsername02.php">
        <table>
            <tr>
                <th><label for="securityAnswer">Security Answer:</label></th>
                <td><input type="text" name="securityAnswer" id="securityAnswer" required</td>
            </tr>

            <tr>
                <th>Submit:</th>
                <td><input type="submit" name="submitAnswer" value="submitAnswer"</td>
            </tr>
        </table>
    </form>

    <?php
}

require_once "footer02.php";
?>

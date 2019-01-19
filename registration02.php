<?php
/**
 * Created by PhpStorm.
 * User: Tyler
 * Date: 3/7/2017
 * Time: 10:42 AM
 */

// This is OUTSIDE of the <body> tag due to the external 'header02.php'
//<script src="validateForm02.js"></script>
//echo "<script src='validateForm02.js'></script>"; // Have to use an echo to include the script tag because you are in the PHP part of the code and JS is used in the HTML part of the code???

//include_once "connect02.php";
require_once "header02.php";

/**
 * JavaScript relies on the DOM which won't pick up on 'elements ID' if it has not been declared before the JavaScript is called.
 */
//<script src="validateForm02.js"></script>
//echo "<script src='validateForm02.js'></script>"; // Have to use an echo to include the script tag because you are in the PHP part of the code and JS is used in the HTML part of the code???

$pageTitle = "Registration";
$errormsg = "";
$showForm = 1;

// Variables for Errors
$usernameTaken = false;
$emailTaken = false;
$passValidError = false;
$passMatchedError = false;


if(isset($_POST['submit']))
{
    /****************************************************************************
     * CREATE NEW VARIABLES TO STORE USER DATA INPUT & SANITIZE DATA FROM USER.
     *****************************************************************************/
    $formfield['firstNameInput'] = trim($_POST['firstNameInput']);
    $formfield['lastNameInput'] = trim($_POST['lastNameInput']);
    $formfield['usernameInput'] = trim(strtolower($_POST['usernameInput']));
    $formfield['emailInput'] = trim(strtolower($_POST['emailInput']));
    $formfield['securityQuestionInput'] = trim($_POST['securityQuestionInput']);
    $formfield['securityAnswerInput'] = trim($_POST['securityAnswerInput']);
    $formfield['passwordInput'] = trim($_POST['passwordInput']);
    $formfield['confirmPasswordInput'] = trim($_POST['confirmPasswordInput']);

    /*********************************************************
     * CHECK FOR EMPTY FIELDS
     ************************************************************/
    if(empty($formfield['firstNameInput'])) {$errormsg .= "<p>The first name is empty.</p>";}
    if(empty($formfield['lastNameInput'])) {$errormsg .= "<p>The last name is empty.</p>";}
    if(empty($formfield['usernameInput'])) {$errormsg .= "<p>The username is empty.</p>";}
    if(empty($formfield['emailInput'])) {$errormsg .= "<p>The email is empty.</p>";}
    if(empty($formfield['securityQuestionInput'])) {$errormsg .= "<p>The security question is empty.</p>";}
    if(empty($formfield['securityAnswerInput'])) {$errormsg .= "<p>The security answer is empty.</p>";}
    if(empty($formfield['passwordInput'])) {$errormsg .= "<p>The password is empty.</p>";}
    if(empty($formfield['confirmPasswordInput'])) {$errormsg .= "<p>The confirm password is empty.</p>";}

    /********************************************************************
     * CHECK FOR PASSWORD VALIDATION WITH regex (regular expression)
     * Requirements:
     * - Must be a minimum of 8 characters
     * - Must contain at least 1 number
     * - Must contain at least one uppercase character
     * - Must contain at least one lowercase character
     *******************************************************************/
    // Instead of creating one big regex (regular expression)
    // Splitting it up is far easier to figure out for someone else looking at your code

    $uppercase = preg_match('@[A-Z]@', $formfield['passwordInput']); // Must contain a Uppercase character
    $lowercase = preg_match('@[a-z]@', $formfield['passwordInput']); // Must contain a Lowercase character
    $number    = preg_match('@[0-9]@', $formfield['passwordInput']); // Must contain a Number character

    if(!$uppercase || !$lowercase || !$number || strlen($formfield['passwordInput']) < 8) // Must be at least 8 characters long
    {
        //create an error message
        $passValidError = true;
        $errormsg .= "<p>Error: Your password is missing one or more of the requirements: <br/>" .
            "- Must be a minimum of 8 characters <br/>" .
            "- Must contain at least 1 number <br/>" .
            "- Must contain at least one uppercase character <br/>" .
            "- Must contain at least one lowercase character</p>" ;
    }

    /**********************************************************************************
     * CHECK FOR MATCHING PASSWORD AND CONFIRM PASSWORD
     *******************************************************************************/
    if($formfield['passwordInput'] != $formfield['confirmPasswordInput'])
    {
        $passMatchedError = true;
        $errormsg .= "<p>The password do not match.</p>";
    }

    /***************************************************************************************
     * CHECK FOR DUPLICATE USERS
     *********************************************************************************/
    try
    {
        /* Create a query string to grab from the database a username that the user had inputted*/
        /* $sqlusers = "SELECT * FROM User WHERE username='" . $formfield['usernameInput'] . "'"; */
        $sqlusers = "SELECT * FROM User WHERE username = :uname";
        $stmtusers = $pdo->prepare($sqlusers);
        $stmtusers->bindValue(':uname', $formfield['usernameInput']);
        $stmtusers->execute();
        $countusers = $stmtusers->rowCount();
        if ($countusers > 0)
        {
            $usernameTaken = true;
            $errormsg .= "<p>The username is already taken. </p>";
        }
    }
    catch (PDOException $e)
    {
        echo "<div class='error'><p>ERROR selecting users from database for duplication check!" .$e->getMessage() . "</p></div>";
    }

    /*********************************************************************8
     * CHECK FOR DUPLICATE EMAILS
     ***********************************************************************/
    try
    {
        /* Create a query string to grab from the database an email that the user had inputted */
        $sqlusers = "SELECT * FROM User WHERE email = :email";
        $stmtusers = $pdo->prepare($sqlusers);
        $stmtusers->bindValue(':email', $formfield['emailInput']);
        $stmtusers->execute();
        $countemails = $stmtusers->rowCount();
        if($countemails > 0)
        {
            $emailTaken = true;
            $errormsg .= "<p>The email is already taken. </p>";
        }
    }
    catch (PDOException $e)
    {
        echo "<div class='error'><p>ERROR selecting email from database for duplication check!" .$e->getMessage() . "</p></div>";
    }



    //*********************************************************
    // Generate a hash for the password                       *
    //*********************************************************
    $password_hash = password_hash($formfield['passwordInput'], PASSWORD_BCRYPT);


    /* *******************************************************************
           CONTROL FOR ERRORS. IF ERRORS, DISPLAY THEM. IF NOT, CONTINUE WITH FORM PROCESSING.
           *******************************************************************/
    if($errormsg != "")
    {
        echo "<div class='w3-row-padding w3-padding-64 w3-container'><h5 class='w3-padding-32'>THERE ARE ERRORS!</h5>";
        echo $errormsg;
        echo "</div>";
    }
    else
    {
        try
        {
            $sql = "INSERT INTO User (first_name, last_name, username, email, secure_question, secure_answer, password_hash)
                        VALUES (:fname, :lname, :uname, :email, :secureQ, :secureA, :passHash)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':fname', $formfield['firstNameInput']);
            $stmt->bindValue(':lname', $formfield['lastNameInput']);
            $stmt->bindValue(':uname', $formfield['usernameInput']);
            $stmt->bindValue(':email', $formfield['emailInput']);
            $stmt->bindValue(':secureQ', $formfield['securityQuestionInput']);
            $stmt->bindValue(':secureA', $formfield['securityAnswerInput']);
            //$stmt->bindValue(':pass', $formfield['passwordInput']); // NOTE: YOU DO NOT WANT TO STORE A PLAIN TEXT PASSWORD INTO THE DATABASE!!!
            $stmt->bindValue(':passHash', $password_hash);

            // Using the getToken() function above to generate a random salt to store into the database
            // - the random salt will be use to generate a recovery key attach to a url query for password reset
            //$randomSalt = getToken();

            //$stmt->bindValue(':passSalt', $randomSalt);

            $stmt->execute();

            $showForm = 0; //hide the form

            echo "<div class='w3-padding-32'><p>There are no errors. Thank you for registering.</p></div>";

            header("Location: confirm02.php?state=7");
        }
        catch (PDOException $e)
        {
            echo "<div class='w3-padding-32'><p>ERROR inserting data into the database!" .$e->getMessage() . "</p></div>";
            exit();
        }
    }//else errorsmsg
}//if(isset(POST_['submit']))

if($showForm == 1)
{
?>
    <div class="w3-row-padding w3-light-grey w3-padding-64 w3-container">
        <h2>Registration Form</h2>

        <form method="post" action="registration02.php" name="registrationForm" id="registrationForm" onsubmit="return validateform()">
            <fieldset>
                <legend>Registration</legend>
                <table>
                    <tr>
                        <th><label for="firstNameInput">First Name:</label></th>
                        <td><input type="text" name="firstNameInput" id="fname" value="<?php echo isset($_POST['firstNameInput']) ? $_POST['firstNameInput'] : '' ?>" /> <b id="fNameSideNotification" class=""> * required </b> </td>
                    </tr>

                    <tr>
                        <th><label for="lastNameInput">Last Name:</label></th>
                        <td><input type="text" name="lastNameInput" id="lname" value="<?php echo isset($_POST['lastNameInput']) ? $_POST['lastNameInput'] : '' ?>" /> <b id="lNameSideNotification" class=""> * required </b> </td>
                    </tr>

                    <tr>
                        <th><label for="usernameInput">Username:</label></th>
                        <td><input type="text" name="usernameInput" id="uname" value="<?php echo isset($_POST['usernameInput']) ? $_POST['usernameInput'] : '' ?>" /> <?php if($usernameTaken == true){echo '<b class="takenError">* This username is already taken.</b>';}else{echo '<b id="uNameSideNotification" class=""> * required </b>';} ?> </td>
                    </tr>

                    <tr>
                        <th><label for="emailInput">Email:</label></th>
                        <td><input type="email" name="emailInput" id="email" value="<?php echo isset($_POST['emailInput']) ? $_POST['emailInput'] : '' ?>" /> <?php if($emailTaken == true) {echo '<b class="takenError"> * This email is already taken.</b>';}else{echo '<b id="emailSideNotification" class=""> * required </b>';} ?> </td>
                    </tr>

                    <tr>
                        <th><label for="securityQuestionInput">Security Question:</label></th>
                        <td><input type="text" name="securityQuestionInput" id="secureQ"><?php echo isset($_POST['securityQuestionInput']) ? $_POST['securityQuestionInput'] : '' ?></input> <b id="secureQSideNotification" class=""> * required </b> </td>
                    </tr>

                    <tr>
                        <th><label for="securityAnswerInput">Security Answer:</label></th>
                        <td><input type="text" name="securityAnswerInput" id="secureA" value="<?php echo isset($_POST['securityAnswerInput']) ? $_POST['securityAnswerInput'] : '' ?>" /> <b id="secureASideNotification" class=""> * required </b> </td>
                    </tr>

                    <tr>
                        <th><label for="passwordInput">Password:</label></th>
                        <td>
                            <input type="password" name="passwordInput" id="pwd" data-indicator="pwdStrengthBar" value="<?php echo isset($_POST['passwordInput']) ? $_POST['passwordInput'] : '' ?>" />
                            <?php
                                if($passValidError == true)
                                {
                                    echo '<b class="takenError"> * Error: Invalid Password (Your password must contain minimum of 8 characters, an Uppercase, Lowercase, and a Number)</b>';
                                }
                                else
                                {
                                    echo '<b id="passSideNotification" class=""> * required (Password must contain at least 8 characters including an Uppercase, Lowercase, and a Number)</b>';
                                }
                            ?>
                        </td>
                    </tr>

                    <tr>
                        <th><label for="confirmPasswordInput">Confirm Password:</label></th>
                        <td><input type="password" name="confirmPasswordInput" id="confirmPWD" value="<?php echo isset($_POST['confirmPasswordInput']) ? $_POST['confirmPasswordInput'] : '' ?>" /> <?php if($passMatchedError == true){echo '<b class="takenError"> * The password does not matched</b>';}else{echo '<b id="confirmPassSideNotification" class=""> * required </b>';} ?></td>
                    </tr>

                    <tr>
                        <th>Submit:</th>
                        <td><input type="submit" name="submit" value="submit"/></td>
                    </tr>
                </table>
            </fieldset>
        </form>
    </div>


<?php
}/*showForm*/


echo "<script src='validateForm02.js'></script>"; // Have to use an echo to include the script tag because you are in the PHP part of the code and JS is used in the HTML part of the code???

include_once "footer02.php";

?>

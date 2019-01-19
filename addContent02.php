<?php
/**
 * Created by PhpStorm.
 * User: Tyler
 * Date: 3/8/2017
 * Time: 4:02 AM
 */

require_once "header02.php";
//session_start(); // (Has already been initiated in the 'header02.php')

// Initialized necessary variables
$pageTitle = "addContent02";
$showAddContentForm = 1;
$errormsg = "";

/**
 * Check to see if $_SESSION['id'] is set: If user is logged in
 */
if(isset($_SESSION['id']))
{
    $userID = $_SESSION['id'];

    //echo "<div><p>The SESSION['id] = ". $_SESSION['id'] ."</p></div>";
}
else
{
    $errormsg .= "There isn't a user logged in: _SESSION['id'] is not set/initialized/declared when in 'login02.php'";
}



/****************************************
 * If the user click the submit button
 ****************************************/
if(isset($_POST['submitContent']))
{
    /******************************************************************
     * Create Variables to store user data from the 'addContentForm'
     *******************************************************************/
    $contentForm['contentTitle'] = $_POST['contentTitle'];
    $contentForm['contentDescription'] = $_POST['contentDescription'];

    /*
    $contentTitle = $_POST['contentTitle'];
    $contentDescription = $_POST['contentDescription'];
    */


    /**************************************
     * Check for empty fields
     *************************************/
    if(empty($contentForm['contentTitle'])) {$errormsg .= 'The content title field is empty';}
    if(empty($contentForm['contentDescription'])) {$errormsg .= 'The content description is empty';}

    /*****************************************************************************************************************
     *  Check for Errors:
     *  - If there are errors, display them
     *  - Else: continue to process the form and insert the content into the User.Content database
     ****************************************************************************************************************/
    if($errormsg != "")
    {
        echo "<div class='w3-row-padding w3-padding-64 w3-container'><h5 class='w3-padding-32'>THERE ARE ERRORS: </h5>";
        echo $errormsg;
        echo "</div>";
    }
    else
    {
        /*
         * Create a SQL statement that will insert the content title and description into the User.Content database
         * - With the associated user (the current user that is logged in)
         */
        try
        {
            // Use to get the current time
            date_default_timezone_set('US/Eastern');
            $currentTime = date('Y-m-d G:i:s');

            $sqlContent = "INSERT INTO Content (content_title, content_description, input_time, user_id)
                           VALUES (:cTitle, :cDescription, :input_time, :user_id)";
            $stmtContent = $pdo -> prepare($sqlContent);
            $stmtContent -> bindValue(':cTitle', $_POST['contentTitle']);
            $stmtContent -> bindValue(':cDescription', $_POST['contentDescription']);
            $stmtContent -> bindValue(':input_time', $currentTime);
            $stmtContent -> bindValue(':user_id', $userID);
            $stmtContent -> execute();

            $showAddContentForm = 0;

            echo "<div class='success'><p>Content Added.</p><br>
                                             <p>Back to <a href='myContent02.php'>My Content</a> </p></div>";
        }
        catch(PDOException $e)
        {
            echo "<div class='error'><p>ERROR inserting content data into the database!" .$e->getMessage() . "</p></div>";
        }

    }
}


if($showAddContentForm == 1) {
    ?>


    <div class="w3-row-padding w3-light-grey w3-padding-64 w3-container">
        <h2>Add Content</h2>

        <form method="post" action="addContent02.php" name="addContentForm">
            <fieldset>
                <legend>Add Content</legend>
                <table>
                    <tr>
                        <th><label for="contentTitle">Title:</label></th>
                        <td><input type="text" name="contentTitle" id="contentTitle" required></td>
                    </tr>
                    <tr>
                        <th><label for="contentDescription">Description:</label></th>
                        <td><textarea name="contentDescription" id="contentDescription" rows="15" cols="80"></textarea></td>
                        <!--"elm1" comes from the tinyMCE javascript in the 'header02.php'-->
                    </tr>
                    <tr>
                        <th>Submit</th>
                        <td><input type="submit" name="submitContent" value="Submit"></td>
                    </tr>
                </table>
            </fieldset>
        </form>
    </div>

    <?php
} // if($showAddContentForm == 1)

include_once "footer02.php";
?>

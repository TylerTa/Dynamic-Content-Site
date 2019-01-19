<?php
/**
 * Created by PhpStorm.
 * User: Tyler
 * Date: 3/12/2017
 * Time: 4:53 AM
 */

$pagetitle = "Update Content Form";
$pageh1 = "Update Content";
$errormsg = "";

require_once "header02.php";

/**
 * 1.) Grab the current 'contentID' from the URL through the $_GET[''] global array variable
 *
 * 2.) Grab the current content from the database and display them in the updateContentForm
 *     - Create a SQL statement to pull the data using the 'contentID' from the url using the $_GET[''] global array variable
 *
 * 3.) Insert the update content into the database when the user click the submit button
 */

/**
 * Check to see which type of user is logged in: An Admin or a Regular User
 */
if(isset($_SESSION['userType']))
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
}
else
{
    $errormsg .= "<p>Error: 'userType' was not set in the SESSION</p>";
}

/*
 * Grabbing the 'contentID' from URL through the $_GET[''] glabal array variable
 * - assigning the value to a variable called '$currentContentID'
 */
if(isset($_GET['contentID']))
{
    $currentContentID = $_GET['contentID'];
}
else
{
    $errormsg .= "Error: The '?contentID=#' is not set in the URL!!!";
}

/*
 * If - the user hit the submit button to update the content: then attempt to process the update
 */
if(isset($_POST['submitUpdatedContent']))
{
    //Assigned some variable to use from the $_POST['']
    $updateContentTitle = $_POST['updateContentTitle'];
    $updateContentDescription = $_POST['updateContentDescription'];

    date_default_timezone_set('US/Eastern');
    $updatedTime = date('Y-m-d G:i:s');

    try
    {
        $sqlUpdateContent = "UPDATE Content SET content_title = :contentTitle, content_description = :contentDescription, update_time = :updateTime WHERE content_id = :currentContentID";
        $stmtUpdateContent = $pdo -> prepare($sqlUpdateContent);
        $stmtUpdateContent -> bindValue(':contentTitle', $updateContentTitle);
        $stmtUpdateContent -> bindValue(':contentDescription', $updateContentDescription);
        $stmtUpdateContent -> bindValue(':updateTime', $updatedTime);
        $stmtUpdateContent -> bindValue(':currentContentID', $currentContentID);
        $stmtUpdateContent -> execute();

        // Direct the user to a confirmation page displaying that the current content was updated!!!
        header("Location: confirm02.php?state=6");
    }
    catch(PDOException $e)
    {
        echo "<div class='error'><p>ERROR: UPDATING content data from the 'updateContentForm' to the database". $e->getMessage() ."</p></div>";
    }
}

/*
 * If - there are $errormsg: then display them
 * Else - Attempt to SELECT the currrent content data from the database
 */
if($errormsg != "") // If the $errormsg is not NULL/Empty: Display the error message
{
    echo "<div class='error'><p>". $errormsg ."</p></div>";
}
else // Else: Attempt to pull the current content data from the database
{
    try
    {
        $sqlCurrentContent = "SELECT content_title, content_description FROM Content WHERE content_id = :contentID";
        $stmtCurrentContent = $pdo -> prepare($sqlCurrentContent);
        $stmtCurrentContent -> bindValue(':contentID', $currentContentID);
        $stmtCurrentContent -> execute();
        $currentContentRow = $stmtCurrentContent -> fetch();
        $contentRowCount = $stmtCurrentContent -> rowCount();

        if($contentRowCount < 1) // If there was no content data SELECTED from the database, display an error
        {
            echo "<div class='error'><p>Error: There was no data SELECTED from the database with the 'contentID' from the URL!</p></div>";
        }
        else // Assign some variables of the current content data to be used to display in the updateContentForm
        {
            $currentContentTitle = $currentContentRow['content_title'];
            $currentContentDescription = $currentContentRow['content_description'];
        }

    }
    catch (PDOException $e)
    {
        echo "<div class='error'><p>ERROR: SELECTING content data from the database to display onto the updateContentForm!". $e->getMessage() ."</p></div>";
    }
}




?>

<div class="w3-row-padding w3-light-grey w3-padding-64 w3-container">
    <h3>Update Content</h3>
    <form method="post" action="updateContent02.php?contentID=<?php echo $currentContentID ?>" name="updateContentForm">
        <fieldset>
            <table>
                <tr>
                    <th><label for="contentTitle">Title:</label></th>
                    <td><input type="text" name="updateContentTitle" id="contentTitle" value="<?php echo $currentContentTitle ?>"required></td>
                </tr>
                <tr>
                    <th><label for="contentDescription">Description:</label></th>
                    <td><textarea name="updateContentDescription" id="contentDescription" rows="15" cols="80"><p><?php echo $currentContentDescription ?></p></textarea></td>
                    <!--"elm1" comes from the tinyMCE javascript in the 'header02.php'-->
                </tr>
                <tr>
                    <th>Submit</th>
                    <td><input type="submit" name="submitUpdatedContent" value="Submit"></td>
                </tr>
            </table>
        </fieldset>
    </form>

</div>

<?php

include_once "footer02.php";

?>
<?php
/**
 * Created by PhpStorm.
 * User: Tyler
 * Date: 3/20/2017
 * Time: 10:33 PM
 */

$errormsg = "";
$pageTitle = "View Content Detail";
$showContentDetail = false;
$getContentID = "";

require_once "header02.php";

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
/*
else
{
    $errormsg .= "<p>Error: 'userType' was not set in the SESSION</p>";
}
*/

/**
 * 1.) Grab the 'contentID' from the URL through the $_GET[''] global array variable
 *     - Check to see: if the 'contentID' variable from the $_GET[''] 'isset'
 *     - Initialized the a variable for the contentID
 */
if(isset($_GET['contentID']))
{
    // Gotta check just in case user try to edit the URL and leave 'contentID' as a "" empty string
    if($_GET['contentID'] == "")
    {
        $errormsg .= "<p>Error: 'contentID' is set as an empty string in the URL.</p>";
        //echo "<div class='error'>". $errormsg ."</div>";
    }
    else // Initialized a variable to hold the 'contentID'
    {
        $contentID = $_GET['contentID'];
    }
}
else // Display an error message: that the 'contentID' was not set in the URL
{
    $errormsg .= "<p>Error: 'contentID' was not set in the URL.</p>";
    //echo "<div class='error'>". $errormsg ."</div>";
}

/**
 * 2.) If: errormsg is NOT an empty string, then it means that there are errors
 *         - Display that there are error
 *
 *     Else: Create an SQL statement that SELECT * FROM Content Where content_id = :contentID
 *         - ':contentID' will be bind with the variable '$contentID' set from the URL $_GET['contentID'] global array variable
 */
if($errormsg != "")
{
    echo "<div class='error'>". $errormsg ."</div>";
}
else
{
    try
    {
        $sqlContentDetail = "SELECT * FROM Content WHERE content_id = :contentID";
        $stmtContentDetail = $pdo -> prepare($sqlContentDetail);
        $stmtContentDetail -> bindValue(':contentID', $contentID);
        $stmtContentDetail -> execute();
        $contentDetailRow = $stmtContentDetail -> fetch();
        $contentDetailRowCount = $stmtContentDetail -> rowCount();

        if($contentDetailRowCount < 1)
        {
            echo "<div class='error'><p>content_id was not found</p></div>";
        }
        else // Initialized variables to hold the content_detail 'fetch' from the database & set $showContentDetail = true;
        {
            $contentTitle = $contentDetailRow['content_title'];
            $contentDescription = $contentDetailRow['content_description'];
            $contentPostTime = $contentDetailRow['input_time'];
            $contentUpdateTime = $contentDetailRow['update_time'];


            $showContentDetail = true;
        }

    }
    catch(PDOException $e)
    {
        echo "<div class='error'><p>Error: There was an error when trying to SELECT the content detail from the database</p></div>";
    }
}

if($showContentDetail == true)
{
    ?>
    <div>
        <fieldset>
            <table>
                <tr>
                    <th><label for="content_id">Content ID:</label></th>
                    <td><?php echo $contentID ?></td>
                </tr>
                <tr>
                    <th><label for="content_title">Title:</label></th>
                    <td><?php echo $contentTitle ?></td>
                </tr>
                <tr>
                    <th><label for="content_description">Description:</label></th>
                    <td><?php echo $contentDescription ?></td>
                </tr>
                <tr>
                    <th><label for="content_input_time">Posted On:</label></th>
                    <td><?php echo $contentPostTime ?></td>
                </tr>
                <tr>
                    <th><label for="content_update_time">Update On:</label></th>
                    <td><?php echo $contentUpdateTime?></td>
                </tr>

                <?php
                    if($userType == 1) { echo "<tr><td><a href='updateContent02.php?contentID=". $contentID ."'>Update Content</a></td>
                                                   <td><a href='deleteContent02.php?contentID=". $contentID ."'>Delete Content</a></td></tr>";}
                ?>
            </table>
        </fieldset>
    </div>
    <?php
}



include_once "footer02.php";
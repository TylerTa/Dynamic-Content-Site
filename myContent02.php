<?php
/**
 * Created by PhpStorm.
 * User: Tyler
 * Date: 3/10/2017
 * Time: 11:58 PM
 */

require_once "header02.php";

$pageTitle ="myContent02.php";
$errormsg = "";

/*************************************************************************************
 * Check to see if the user is logged in through the $_SESSION[''] array variable.
 *************************************************************************************/

if(isset($_SESSION['id']))
{
    $userID = $_SESSION['id'];
}
else
{
    $errormsg .= "Error: User ID is not set in the _SESSION['id'] (User might not be logged in)";
}

if(isset($_SESSION['uname']))
{
    $username = $_SESSION['uname'];
}
else
{
    $errormsg .= "Error: Username is not set in the _SESSION['uname'] (User might not be logged in)";
}

if(isset($_SESSION['firstName'])) {echo "<div class='success'><p>Hello, " . $_SESSION['firstName'] . "! You are currently logged in!</p></div>";}


/**
 * Check to see if there are errors: Display/echo the errors to the screen
 * - Else: proceed in grabbing the Content from the database and display them to the screen
 */
if($errormsg != "") // If $errormsg is NOT null/empty: then there have been errors assigned to it
{
    echo "<div class='error'><p>There are errors: <br> " . $errormsg . "</p></div>";
}
else
{
    /*
     * Create a SQL statement that grabs all the content associated with the user id ($_SESSION['id'])
     * - How would we iterate through the content grabbed and display it in a nice format?
     * - Should I using the foreach() method to iterate through the array?
     * - Should I grab the rowCount() from the SQL statement and use a while loop for format each content with with an html styling?
     */
    try
    {
        $sqlContent = "SELECT DISTINCT content_id, content_title, content_description, input_time, update_time FROM Content WHERE user_id = :userID";
        //$sqlContent = "SELECT *"
        $stmtContent = $pdo->prepare($sqlContent);
        $stmtContent->bindValue(':userID', $userID);
        $stmtContent->execute();
        //$contentRow = $stmtContent->fetch();
        $contentRowCount = $stmtContent->rowCount();

        if($contentRowCount < 1) // If there was any content pull from the database: Display a message to the user
        {
            echo "<p class='error'>There is no content from this user in the database</p>";
        }
        else
        {

            // Use a while loop as it is fetching each 'row' of data pulled from the database
            while($contentRow = $stmtContent->fetch())
            {
            ?>
                <fieldset>
                    <table>
                        <tr>
                            <th><label for="content_id">Content ID:</label></th>
                            <td><?php echo $contentRow['content_id'] ?></td>
                        </tr>
                        <tr>
                            <th><label for="content_title">Title:</label></th>
                            <td><?php echo $contentRow['content_title'] ?></td>
                        </tr>
                        <tr>
                            <th><label for="content_description">Descripition:</label></th>
                            <td><?php echo $contentRow['content_description'] ?></td>
                        </tr>
                        <tr>
                            <th><label for="input_time">Posted On:</label></th>
                            <td><?php echo $contentRow['input_time'] ?></td>
                        </tr>
                        <tr>
                            <th><label for="update_time">Updated On:</label></th>
                            <td><?php echo $contentRow['update_time'] ?></td>
                        </tr>
                        <tr>
                            <?php echo "<td><a href='updateContent02.php?contentID=". $contentRow['content_id'] ."'>Update Content</a></td>" ?>
                            <?php echo "<td><a href='deleteContent02.php?contentID=". $contentRow['content_id'] ."'>Delete Content</a></td>"?>
                        </tr>

                    </table>
                </fieldset>
            <?php
            } // while($contentRow = $stmtContent->fetch())

        }
    }
    catch(PDOException $e)
    {
        echo "<div class='error'>Error fetching content: ". $e ->getMessage() ."</div>";
    }



}

include_once "footer02.php";

?>
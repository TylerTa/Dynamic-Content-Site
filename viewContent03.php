<?php
/**
 * Created by PhpStorm.
 * User: Tyler
 * Date: 4/16/2017
 * Time: 3:18 PM
 */

$errormsg = "";
$showContent = false;
$pagetitle = "View Content";


require_once "header02.php";

/**
 * Check which type of user (regular/admin) is logged in to give them the permission to edit and delete content
 */
if(isset($_SESSION['userType']))
{
    // Store the 'userType' into a variable
    // If 'userType' == 0 : (Regular User)
    // If 'userType' == 1 : (Admin User)
    $userType = $_SESSION['userType'];
}
/**
else
{
    $errormsg .= "<div><p class='error'>Error: 'userType' was not set in the SESSION</p></div>";
}
 * /

/**
 * Create an SQL statement that pull all of the content detail from the database
 */
if($errormsg != "")
{
    echo "<div class='error'>". $errormsg ."</div>";
}
else
{
    try
    {
        $sqlAllContent = "SELECT content_id, content_title, content_description, input_time, update_time FROM Content ORDER BY content_title";
        $stmtAllContent = $pdo -> prepare($sqlAllContent);
        $stmtAllContent -> execute();

        $contentRowCount = $stmtAllContent -> rowCount();

        if($contentRowCount < 1)
        {
            echo "<div class='error'><p>There is no content available at the moment.</p></div>";
        }
        else
        {
            $showContent = true;
        }
    }
    catch(PDOException $e)
    {
        echo "<div><p class='error'>Error: There was an error when SELECTING all of the content from the database.</p></div>";
    }
}

if($showContent == true)
{
    while($allContentRow = $stmtAllContent -> fetch())
    {
        ?>
        <!-- HTML -->
        <div class="content_title_dropDown success">
            <fieldset>
                <table>
                    <tr>
                        <th><label for="content_title">Content Title:</label></th>
                        <td><p><?php echo $allContentRow['content_title'] ?></p></td>

                    </tr>
                </table>
            </fieldset>
        </div>
        <div class="content_detail success">
            <p>Content ID: <?php echo $allContentRow['content_id']; ?></p>
            <p>Content Title: <?php echo $allContentRow['content_title'] ?></p>
            <p>Content Description: <?php echo $allContentRow['content_description'] ?></p>
            <p>Posted On: <?php echo $allContentRow['input_time'] ?></p>
            <p>Updated On: <?php echo $allContentRow['update_time'] ?></p>

            <?php
            if($userType == 1) { echo "<tr><td><a href='updateContent02.php?contentID=". $allContentRow['content_id'] ."'>Update Content</a></td>
                                                   <td><a href='deleteContent02.php?contentID=". $allContentRow['content_id'] ."'>Delete Content</a></td></tr>";}
            ?>
        </div>

        <br/>


        <?php
    }
}



include_once "footer02.php";
?>
<?php
/**
 * Created by PhpStorm.
 * User: Tyler
 * Date: 3/12/2017
 * Time: 5:03 AM
 */

$pageTitle = "Delete Content";
$pageh1 = "Delete Content";
$errormsg = "";
$showContent = false;

require_once "header02.php";

/**
 * Check to see which type of user is logged in: An Admin or a Regular User
 */
if(isset($_SESSION['userType'])) //This code was repeated a lot on each page...maybe i could include it into my header page?
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

/**
 * 1.) Grab the contentID from the URL using a $_GET global array variable and initialized it into a variable
 */
if(isset($_GET['contentID']))
{
    $currentContentID = $_GET['contentID'];
}
else
{
    $errormsg .= "<p>Error: The '?contentID=#' is not set in the URL!!!</p>";
}

// 2.) Check and see if the $_SESSION['id'] is set for you current user ID
if(isset($_SESSION['id']))
{
    $currentUserID = $_SESSION['id'];

    //echo "<div>Current User ID: ". $currentUserID ."</div>"; // Testing: To see if the user is currently logged in
}
else
{
    $errormsg .= "Error: The 'SESSION['id'] is not set - User is not logged in!";
}

// 3.) Create an SQL statement that pulls the contentID Where User id = $_SESSION['id']
if($errormsg != "")
{
    echo "<div class='error'>". $errormsg ."</div>";
}
else
{
    /*
     * If: the User hit the 'submit' button to confirm deletion of content: then process the deletion of the content
     *     - If: A regular user is logged in, then write a SQL statement associated with the 'user_id'
     *     - Else If: the user is an ADMIN, then write a SQL statement associated to the ADMIN user
     *
     * Else: Create a SQL Statement to DELETE content.
     */
    if(isset($_POST['confirmDeleteButton']))
    {
        // If: a regular User is logged in
        // - Create an SQL statement that will delete the content WHERE content_id = GET['contentID'] AND user_id = SESSION['id']
        if($userType == 0)
        {
            try
            {
                $sqlDeleteContent = "DELETE FROM Content WHERE content_id = :contentID AND user_id = :userID";
                $stmtDeleteContent = $pdo->prepare($sqlDeleteContent);
                $stmtDeleteContent->bindValue(':contentID', $currentContentID);
                $stmtDeleteContent->bindValue(':userID', $currentUserID); // $currentUserID is initialized through the SESSION['id'] variable to also make sure that only the current user that is logged in may delete his/her own content.
                $stmtDeleteContent->execute();

                header("Location: confirm02.php?state=8");
            }
            catch (PDOException $e)
            {
                echo "<div class='error'><p>Error: There was an error DELETING from the database as an REGULAR USER with the 'contentID' from the URL & the 'userID' from the SESSION['']!</p></div>";
            }
        }
        else if($userType == 1) // Create an SQL statement that will delete the content WHERE content_id = GET['contentID']
        {
            try {
                $sqlAdminDelete = "DELETE FROM Content Where content_id = :contentID";
                $stmtAdminDelete = $pdo->prepare($sqlAdminDelete);
                $stmtAdminDelete->bindValue(':contentID', $currentContentID);
                $stmtAdminDelete->execute();

                header("Location: confirm02.php?state=8");
            }
            catch (PDOException $e)
            {
                echo "<div><p class='error'>Error: There was an error DELETING from the database as an ADMIN</p></div>";
            }
        }
    }
    else //
    {
        // If: the ('$userType' == 0) Create a SQL Statement associated with a 'Regular User'
        // Else If: the ($userType == 1) Create an SQL statement associated with a 'ADMIN User'
        if($userType == 0) // If the '$userType' is a 'Regular User'
        {
            try {
                $sqlSelectContent = "SELECT content_title, content_description FROM Content WHERE content_id = :contentID AND user_id = :userID";
                $stmtSelectContent = $pdo->prepare($sqlSelectContent);
                $stmtSelectContent->bindValue(':contentID', $currentContentID);
                $stmtSelectContent->bindValue(':userID', $currentUserID);
                $stmtSelectContent->execute();
                $contentRow = $stmtSelectContent->fetch();
                $contentRowCount = $stmtSelectContent->rowCount(); // Grab the number of rows pull from the database. (Should be only one row of Content)

                if ($contentRowCount < 1) // If there was no row pulled from the database, display the error.
                {
                    echo "<div class='error'><p>Error: There was no data SELECTED from the database with the 'contentID' from the URL & the 'userID' from the SESSION['']!</p></div>";
                } else // Display the content and ask the user if he or she would like to delete said content with a button
                {

                    $showContent = true; // Set to true to display the html form of the content. Check the 'if($showContent == true)' below.
                }
            }
            catch (PDOException $e)
            {
                echo "<div class='error'><p>ERROR: SELECTING content data from the database associated with a 'REGULAR' logged in user, using the \'currentContentID\' WHERE User id = \'currentUserID\'" . $e->getMessage() . "</p></div>";
            }
        }
        else if($userType == 1)  // If the '$userType' is an "ADMIN User'
        {
            try
            {
                $sqlAdminSelectContent = "SELECT content_title, content_description FROM Content WHERE content_id = :contentID";
                $stmtAdminSelectContent = $pdo -> prepare($sqlAdminSelectContent);
                $stmtAdminSelectContent -> bindValue(':contentID', $currentContentID);
                $stmtAdminSelectContent -> execute();
                $contentRow = $stmtAdminSelectContent -> fetch();
                $contentRowCount = $stmtAdminSelectContent -> rowCount();

                if ($contentRowCount < 1)
                {
                    echo "<div><p class='error'>Error: There was no data SELECTED from the database with the 'contentID' from the URL.</p></div>";
                }
                else
                {
                    $showContent = true;
                }
            }
            catch (PDOException $e)
            {
                echo "<div class='error'><p>ERROR: SELECTING content data from the database associated with an 'ADMIN user', using the \'currentContentID\' WHERE content_id = \'currentContentID\'" . $e->getMessage() . "</p></div>";
            }
        }
    }
}

// If: The $showContent variable is set to true display then html form of the detailed content.
// - (The $showContent variable is set true if there was data pulled from the database SELECTION successfully)
if($showContent == true)
{
?>
    <div class='success'>
        <b>Are you sure you would like to delete content?</b>
        <form method="post" action="deleteContent02.php?contentID=<?php echo $currentContentID ?>" name="deleteContentForm">
            <fieldset>
                <table>
                    <tr>
                        <th><label for="contentTitle">Title: </label></th>
                        <td><?php echo $contentRow['content_title'] ?></td>
                    </tr>

                    <tr>
                        <th><label for="contentDescription">Description: </label></th>
                        <td><?php echo $contentRow['content_description'] ?></td>
                    </tr>
                </table>
                <input type="submit" name="confirmDeleteButton" value="Yes">  <!-- a 'submit' button that process the deletion of content -->

                <a href="myContent02.php"> <input type="button" name="cancelDeleteButton" value="No"> </a> <!-- A cancel button that leads back to myContent02.php page -->
            </fieldset>
        </form>
    </div>


<?php
}

// 4.) Display the content and ask the user if he or she would like to delete said content with a button

// 5.) Once user has click the button to confirm deletion, go back up this page/reload with action='deleteContent02.php'
//     - Create a SQL statement that delete/remove the content Where User id = $_SESSION['id'];
/*
 * (Should I store the content ID into another $_SESSION[''] to be used once the page has reloaded itself?)
 * (OR should I reload the page with the contentID still attached to the URL when i click the confirm delete button using action='deleteContent02.php=?'. $_GET['']
 */
//     - After when the SQL query is performed without any errors
//     - Send the user to the confirm02.php Using the header() function;

// 6.)  Once on the confirm02.php page, the state should Display to the User that the content was successfully deleted & Lead the user back to myContent02.php


include_once "footer02.php";
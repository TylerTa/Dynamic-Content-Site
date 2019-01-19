<?php
/**
 * Created by PhpStorm.
 * User: Tyler
 * Date: 3/21/2017
 * Time: 2:58 AM
 */

$errormsg = "";
$pagetitle = "Edit User";
$showConfirmation = false;
$userTypeString = "";
$userTypeChange = "";
$userTypeIntChange = null;

require_once "header02.php";

/**
 * Check to see if an ADMIN User is logged in
 */
if(isset($_SESSION['userType'])) // Check to see if $_SESSION['userType'] isset
{
    // Store the 'userType' into a variable
    // If 'userType' == 0 : (Regular User)
    // If 'userType' == 1 : (Admin User)
    $userType = $_SESSION['userType'];

    if($userType == 0)
    {
        //$userTypeString = "Regular";
        //$userTypeChange = "ADMIN";
        echo "<div><p class='error'>You are currently logged in as a 'Regular' user type</p></div>";
    }
    else if($userType == 1)
    {
        //$userTypeString = "Admin";
        //$userTypeChange = "REGULAR";
        echo "<div><p class='error'>You are currently logged in as an 'Admin' user type</p></div>";
    }
}
else
{
    $errormsg .= "<p>Error: 'userType' was not set in the SESSION</p>";
}

/**
 * Grab the user id from the URL through $_GET['userId']
 */
if(isset($_GET['userID']))
{
    if($_GET['userID'] == "") // If: the 'userID' is set to an empty string ""
    {
        $errormsg .= "<p class='error'>Error: the 'userID' is set as an empty string </p>";
    }
    else
    {
        $userID = $_GET['userID'];
    }
}
else
{
    $errormsg .= "<p class='error'>Error: there was no 'userID' set in the URL </p>";
}


/**
 * Grab the username from the URL through $_GET['username']
 */
if(isset($_GET['username']))
{
    if($_GET['username'] == "") // If: the 'userID' is set to an empty string ""
    {
        $errormsg .= "<p class='error'>Error: the 'username' is set as an empty string </p>";
    }
    else {
        $username = $_GET['username'];
    }
}
else
{
    $errormsg .= "<p class='error'>Error: there was no 'username' set in the URL </p>";
}

/**
 * Grab the userType from the URL through $_GET['username']
 */
if(isset($_GET['userType']))
{
    if($_GET['userType'] == "") // If: the 'userID' is set to an empty string ""
    {
        $errormsg .= "<p class='error'>Error: the 'userType' is set as an empty string </p>";
    }
    else
    {
        $userType = $_GET['userType'];

        // Create an if statement to assign the userType into a string format
        if($userType == 0)
        {
            $userTypeString = "Regular";
            $userTypeChange = "ADMIN";
            $userTypeIntChange = 1; // Use to update the database of the user_type status change
        }
        else if($userType == 1)
        {
            $userTypeString = "Admin";
            $userTypeChange = "REGULAR";
            $userTypeIntChange = 0; // Use to update the database of the user_type status change
        }
    }
}
else
{
    $errormsg .= "<p class='error'>Error: there was no 'userType' set in the URL </p>";
}

/**
 * If there are no errors, then set the $showConfirmation to 'true' and display the user info to change user status
 */
if($errormsg != "")
{
    echo "<div>". $errormsg ."</div>";
}
else
{
    /**
     * If the user click the submit 'Yes' button
     * - Create an SQL statement the change the user type
     */
    if(isset($_POST['confirmUserChange']))
    {
        // Create an SQL Statement that changes the user_type status of the current user with $_GET['userID']
        try
        {
            $sqlUserTypeUpdate = "UPDATE User SET user_type = :userType WHERE id = :userID";
            $stmtUserTypeUpdate = $pdo ->prepare($sqlUserTypeUpdate);
            $stmtUserTypeUpdate -> bindValue(':userType', $userTypeIntChange);
            $stmtUserTypeUpdate -> bindValue(':userID', $userID);
            $stmtUserTypeUpdate -> execute();

            header("Location: confirm02.php?state=9");
        }
        catch (PDOException $e)
        {
            echo "<div><p class='error'>Error: There was an error UPDATING User user_type status from the database</p></div>";
        }
    }
    else // Display the confirmation form
    {
        $showConfirmation = true;
    }
}

/**
 * If $showConfirmation is true display the user info with a button to confirm changing userType status
 */
if($showConfirmation == true)
{
    ?>
    <div class="success">
        <b>Would you like to change this user status to an <?php echo $userTypeChange ?> User?</b>
        <form method="post" action="editUser02.php?userID=<?php echo $userID ?>&username=<?php echo $username ?>&userType=<?php echo $userType ?>" name="editUserForm">
            <fieldset>
                <table>
                    <tr>
                        <th><label for="username">Username:</label></th>
                        <td><?php echo $username ?></td>
                    </tr>
                    <tr>
                        <th><label for="userType">User Type:</label></th>
                        <td><?php if($userType == 0) {echo "Regular User";}
                            else if($userType == 1) {echo "Admin User";}?>
                        </td>
                    </tr>
                </table>

                <!-- a 'submit' button that process the userType status change -->
                <input type="submit" name="confirmUserChange" value="Yes">

                <!-- A cancel button that leads back to registeredUser02.php page -->
                <a href="registeredUser02.php"> <input type="button" name="cancelUserChange" value="No"> </a>
            </fieldset>
        </form>
    </div>
    <?php
}


include_once "footer02.php";
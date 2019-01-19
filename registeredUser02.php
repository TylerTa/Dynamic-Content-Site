<?php
/**
 * Created by PhpStorm.
 * User: Tyler
 * Date: 3/21/2017
 * Time: 2:16 AM
 */

$errormsg = "";
$pagetitle = "Registered User";
$showUserList = false;

require_once "header02.php";

/**
 * Check any see if an ADMIN 'userType' is logged in
 */
if(isset($_SESSION['userType'])) // Check to see if $_SESSION['userType'] isset
{
    // Store the 'userType' into a variable
    // If 'userType' == 0 : (Regular User)
    // If 'userType' == 1 : (Admin User)
    $userType = $_SESSION['userType'];

    if($userType == 0)
    {
        echo "<div><p class='error'>You are currently logged in as a 'Regular' user type</p></div>";
    }
    else if($userType == 1)
    {
        echo "<div><p class='error'>You are currently logged in as an 'Admin' user type</p></div>";
    }
}
else
{
    $errormsg .= "<p>Error: 'userType' was not set in the SESSION</p>";
}

/**
 * If an ADMIN 'userType' is logged in then Create a SQL statement to display all registered user.
 * Else display to the screen that only an Admin User is permitted access to this page.
 */
if($userType == 1) // If: $userType is an ADMIN user
{
    try
    {
        $sqlSelectUsers = "SELECT id, username, user_type FROM User ORDER BY username";
        $stmtSelectUsers = $pdo -> prepare($sqlSelectUsers);
        $stmtSelectUsers -> execute();
        //$selectUsersRow = $stmtSelectUsers -> fetch();
        $selectUserRowCount = $stmtSelectUsers -> rowCount();

        if($selectUserRowCount < 1)
        {
            echo "<div><p class='error'>There currently is no User registered in the database</p>";
        }
        else
        {
            $showUserList = true;
        }
    }
    catch (PDOException $e)
    {
        echo "<div><p class='error'>Error: There was an error SELECTING all of the User data from the database</p></div>";
    }
}

if($showUserList == true)
{
    while($selectUsersRow = $stmtSelectUsers -> fetch())
    {
        ?>
        <div class="success">

            <fieldset>
                <table>
                    <tr>
                        <th><label for="username">Username:</label></th>
                        <td><?php echo $selectUsersRow['username'];?></td>
                    </tr>
                    <tr>
                        <th><label for="userType">User Type:</label></th>
                        <td><?php if($selectUsersRow['user_type'] == 0) {echo "Regular User";}
                            else if($selectUsersRow['user_type'] == 1) {echo "Admin User";}?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <a href="editUser02.php?userID=<?php echo $selectUsersRow['id'] ?>&username=<?php echo $selectUsersRow['username']?>&userType=<?php echo $selectUsersRow['user_type'] ?>"><button value="Change Status">Change Status</button></a>
                        </td>
                    </tr>
                </table>
            </fieldset>

        </div>
        <?php
    }
}




include_once "footer02.php";
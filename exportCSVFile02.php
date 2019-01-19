<?php
/**
 * Created by PhpStorm.
 * User: Tyler
 * Date: 4/15/2017
 * Time: 11:51 PM
 */

require_once "header02.php";

/**
 * Once the user submit/click the 'Download CSV' button: process the CSV download
 */


/**
 * Check if user is login in by checking if User 'id' was set in the $SESSION[] array
 * - if(isset($SESSION['id'])): let the user access this page and continue the process
 * - else: alert to the screen that user does not have permission to access this page
 */
if(isset($_SESSION['id']))
{
    /**
     * Create a SQL statement that grab the all the emails from the 'User' database
     */
    try
    {
        $sqlEmails = "SELECT first_name, last_name, email FROM User ORDER BY first_name";
        $stmtEmails = $pdo -> prepare($sqlEmails);
        $stmtEmails -> execute();
        //$contentEmailRow = $stmtEmails -> fetch();
        $contentEmailRowCount = $stmtEmails -> rowCount();
    }
    catch(PDOException $e)
    {
        echo "<div class='error'><p>Error: There was an error when trying to SELECT the first_name, last_name, email from the database.</p></div>" . $e->getMessage();
    }

    /**
     * If no rows were pulled from the database: then notify the user that the User emails was not found.
     * else: display each row content in a table format
     */
    if($contentEmailRowCount < 1)
    {
        echo "<div class='error'><p>Users email was not found.</p></div>";
    }
    else
    {
        echo "<legend class='success'> <h4>List of Users Email</h4> </legend>";

        echo "<div class='success'>
              <table id='emailListTable'>
              <thead>
                <tr>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                </tr>
              </thead>
              <tbody>";

        while($contentEmailRow = $stmtEmails -> fetch())
        {
            echo "<tr>
                    <td>". $contentEmailRow['first_name'] ."</td>
                    <td>". $contentEmailRow['last_name'] ."</td>
                    <td>". $contentEmailRow['email'] ."</td>
                  </tr>";
        }

        echo "</tbody></table></div>";

        /**
         * Create a submit type button to process the 'download CSV file'
         */
        echo "<div class='success'>
                <form name='download_email_csv' action='downloadCSV02.php' method='post'>
                    <input type='submit' name='export_email_csv' value='Download CSV'/> 
                </form>
              </div>";
    }
}
else
{
    echo "<p class='error'>You are currently not logged in and do not have permission to access this page.</p>";
}


?>

<!-- HTML -->
<div id="displayEmail">



</div>



<?php
include_once "footer02.php";

?>
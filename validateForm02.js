/**
 * Created by Tyler on 4/9/2017.
 */
/** Field Form Validation for Registration02.php */





function validateform(){

    var error = 0;
    var elementIDName = "";


    /**
     * Check the first name field form for an empty field
     */
    if(document.getElementById("fname").value == "")
    {
        document.getElementById("fNameSideNotification").className = "takenError";
        document.getElementById("fNameSideNotification").innerHTML = "Error: First Name Required!";

        error = 1;
    }
    else // Required and 'else' statement to turn the side notification back to its original or cleared form once the user re-submit the form again with the required field, but when there are still other fields with empty error notification.
    {
        document.getElementById("fNameSideNotification").className = "";
        document.getElementById("fNameSideNotification").innerHTML = "* Required";

        error = 0;
    }

    /**
     * Check the last name field form for an empty field
     */
    if(document.getElementById("lname").value == "")
    {
        document.getElementById("lNameSideNotification").className = "takenError";
        document.getElementById("lNameSideNotification").innerHTML = "Error: Last Name Required!";

        error = 2;
    }
    else // Required and 'else' statement to turn the side notification back to its original or cleared form once the user re-submit the form again with the required field, but when there are still other fields with empty error notification.
    {
        document.getElementById("lNameSideNotification").className = "";
        document.getElementById("lNameSideNotification").innerHTML = "* Required";

        error = 0;
    }

    /**
     * Check the Username field form for an empty field
     */
    if(document.getElementById("uname").value == "")
    {
        document.getElementById("uNameSideNotification").className = "takenError";
        document.getElementById("uNameSideNotification").innerHTML = "Error: Username Required!";

        error = 3;
    }
    else // Required and 'else' statement to turn the side notification back to its original or cleared form once the user re-submit the form again with the required field, but when there are still other fields with empty error notification.
    {
        document.getElementById("uNameSideNotification").className = "";
        document.getElementById("uNameSideNotification").innerHTML = "* Required";

        error = 0;
    }

    /**
     * Check the Email field form for empty field
     */
    if(document.getElementById("email").value == "")
    {
        error = 4;

        //elementIDName = "emailSideNotification";
        document.getElementById("emailSideNotification").className = "takenError";
        document.getElementById("emailSideNotification").innerHTML = "Error: Email Required!";
    }
    else
    {
        //elementIDName = "emailSideNotification";

        document.getElementById("emailSideNotification").className = "";
        document.getElementById("emailSideNotification").innerHTML = "* Required!!!!!";
        //ffieldSideNoteDefault(elementIDName);

        error = 0;
    }

    /**
     * Check the Security Question text box for empty field
     *
     * Apparently you have to use RegExp: \S to match non whitespace character (anything but not a space, tab, or new line.)
     * Cause fricking a textbox has some kind of invisible whitespace which count as a character?
     *
     * Fucking apparently for a TEXT AREA you should try checking the length of the string!!!
     *
     * I dont know WTF is going on...it will only turn back to the default notification when i submit it twice
     * - Somewhere the getElementById has already been set when you re-submit the form
     *
     * Maybe because we are using a tinyMCE text area form is the problem for repeated submission
     * - try using a tinyMCE.get method
     */

    //var tinyMCEContent = tinyMCE.get('secureQ').getContent();


    //var secureQTextBoxFormFieldString = document.getElementById("secureQ").value;
    //var textAreaLength = secureQTextBoxFormFieldString.length;


    if(document.getElementById("secureQ").value == "")
    {
        error = 5;

        //elementIDName = "secureQSideNotification";
        document.getElementById("secureQSideNotification").className = "takenError";
        document.getElementById("secureQSideNotification").innerHTML = "Error: Security Question Required!";

    }
    else
    {
        //elementIDName = "secureQSideNotification";
        //ffieldSideNoteDefault(elementIDName);
        document.getElementById("secureQSideNotification").className = "";
        document.getElementById("secureQSideNotification").innerHTML = "* Required!!!";

        error = 0;
    }


    /**
     * Check Security Answer for empty field
     */
    if(document.getElementById("secureA").value == "")
    {
        error = 6;
        document.getElementById("secureASideNotification").className = "takenError";
        document.getElementById("secureASideNotification").innerHTML = "Error: Security Answer Required!";
    }
    else
    {
        document.getElementById("secureASideNotification").className = "";
        document.getElementById("secureASideNotification").innerHTML = "* Required";

        error = 0;
    }


    /**
     * Check Password for empty field
     * - Check for a valid password (Password must contain at least 8 characters including an Uppercase, Lowercase, and a Number)
     */
    if(document.getElementById("pwd").value == "")
    {
        error = 7;

        elementIDName = "passSideNotification";
        document.getElementById(elementIDName).className = "takenError";
        document.getElementById(elementIDName).innerHTML = "Error: Password Cannot Be Empty!";
    }
    else
    {
        // Check to valid password (Password must contain at least 8 characters including an Uppercase, Lowercase, and a Number)
        var passwordValidation = document.getElementById("pwd").value;
        var passwordRegexpPattern = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z]{8,}$/;

        if(passwordRegexpPattern.test(passwordValidation))
        {
            elementIDName = "passSideNotification";
            document.getElementById(elementIDName).className = "";
            document.getElementById(elementIDName).innerHTML = "Password is Valid";

            error = 0;
        }
        else // Password is invalid
        {
            elementIDName = "passSideNotification";
            document.getElementById(elementIDName).className = "takenError";
            document.getElementById(elementIDName).innerHTML = "Invalid Password: (Password must contain at least 8 characters including an Uppercase, Lowercase, and a Number.)";

            error = 8;
        }

    }

    /**
     * Check 'Confirm Password' for empty field
     * - Check if the 'Confirm Password' matches the 'Password'
     */
    if(document.getElementById("confirmPWD").value == "")
    {
        error = 9;
        elementIDName = "confirmPassSideNotification";
        document.getElementById(elementIDName).className = "takenError";
        document.getElementById(elementIDName).innerHTML = "Error: Confirm Password Cannot Be Empty!";
    }
    else
    {
        // Check to see if 'confirm password' matches 'password'
        var confirmPass = document.getElementById("confirmPWD").value;
        var password = document.getElementById("pwd").value;
        elementIDName = "confirmPassSideNotification";

        if(confirmPass != password)
        {
            document.getElementById(elementIDName).className = "takenError";
            document.getElementById(elementIDName).innerHTML = "Error: Your Password Does Not Match!";

            error = 10;
        }
        else
        {
            document.getElementById(elementIDName).className = "";
            document.getElementById(elementIDName).innerHTML = "Password Matched!";

            error = 0;
        }

    }



    if(error != 0)
    {
        //window.alert("There are error within the registration form!");

        return false;
    }





} // function validateform()

/**
 * @ffieldSideNoteDefault - This function passes a string representing the ID of an HTML element & set it to the default value when the form field is NOT EMPTY
 * @param elementID: A Valid String representing the ID of an HTML element in the Registration02.php form field
 */
function ffieldSideNoteDefault(elementID)
{
    document.getElementById(elementID).className = "";
    document.getElementById(elementID).innerHTML = "* Required";

    //error = 0;
}

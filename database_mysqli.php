<?php

// Define the connection infos
// $servername = "your_host_ip";
// $username = "your_user";
// $password = "your_password";
// $database = "your_db_name";

$servername = "localhost";
$username = "root";
$password = "L0g1c001";
$database = "accounts";

// Instantiate the connection to the database
$connection = new mysqli($servername, $username, $password, $database);

// Check the connection
if($connection->connect_error)
    die("Connection failed: " . $connection->connect_error . "with error: " . $connection->connect_errno);
echo "Connected successfully";

// Check if a row containing the 'email' or 'password' value sent through the form already exists
$sql_query = "SELECT email, username FROM account_info WHERE email = '{$_POST["email"]}' OR username = '{$_POST["username"]}' LIMIT 1";
$result = mysqli_query($connection, $sql_query);
if(mysqli_num_rows($result) > 0)
    die("<br>There is already a row containing this email or username");

// Check if the submit button has been clicked
if(isset($_POST['send'])) {

    // Assign a default value if an input box hasn't been filled
    if(empty($_POST['firstname']))
        $_POST['firstname'] = 'NoName';

    if(empty($_POST['lastname']))
        $_POST['lastname'] = 'NoLastname';

    if(empty($_POST['gender']))
        $_POST['gender'] = 'M';

    if(empty($_POST['country']))
        $_POST['country'] = 'NoCountry';

    if(empty($_POST['city']))
        $_POST['city'] = 'NoCity';

    if(empty($_POST['birth_date']))
        $_POST['birthdate'] = NULL;


    // Hash the password using the CRYPT_BLOWFISH algorithm whose result will always be a 60 character string
    $hashed_password = password_hash($_POST["pwd"], PASSWORD_BCRYPT);

    // Check if the password inserted in the input box is the same of the hashed one
    if(password_verify($_POST['pwd'], $hashed_password)) {
        echo "<br>Valid password inserted<br>";

        // Send to the database the values submitted via the form inserting them into a new row
        $sql_query = "INSERT INTO account_info ". 
                    "(first_name, last_name, email, username, password, ".
                    "signup_date, gender, country, city, birth_date) ".
                    "VALUES ('{$_POST["firstname"]}', '{$_POST["lastname"]}', '{$_POST["email"]}', '{$_POST["username"]}', '{$hashed_password}', ".
                    "NOW(), '{$_POST['gender']}', '{$_POST["country"]}', '{$_POST["city"]}', '{$_POST["birthdate"]}' )";
        
        if($result = mysqli_query($connection, $sql_query))
            echo "Query sent successfully to the Database";
        else 
            echo "Something went wrong during the query send to the Database";

    }
    else
        die("Invalid password inserted. Something went wrong");

}

// Close the connection (not necessary because it is always closed automatically)
$connection->close();

?>
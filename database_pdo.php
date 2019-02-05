<?php
    // Connection infos
    $dsn = "mysql:dbname=your_db_name;host=your_host_ip";
    $username = "your_user";
    $password = "your_password";

    // Try statement containing all the queries to the database and the prepared statements to prevent SQL injection attacks
    try {

        // Instantiate the connection to the database
        $dbConnection = new PDO($dsn, $username, $password);
        echo "Connected successfully to the Database<br>";
        $dbConnection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        
        // Disable emulated prepared statements to use the real prepared ones
        $sql_query = "SELECT COUNT(*) FROM account_info WHERE email = :email OR username = :username LIMIT 1";
        $preparedStatement = $dbConnection->prepare($sql_query);
        $preparedStatement->bindParam(':email', $_POST['email'], PDO::PARAM_STR);
        $preparedStatement->bindParam(':username', $_POST['username'], PDO::PARAM_STR);
        
        // Array which contains the row fields that can't be equal
        $column_query = array(
            ':email'=>$_POST['email'],
            ':username'=>$_POST['username']
        );
        print_r($column_query);
        
        // Check if a row already contains one of the fields previously saved in the $column_query array
        $preparedStatement->execute($column_query);
        if($column = $preparedStatement->fetchColumn())
            die("<br>There is already a row containing this email and/or username");
        
        // Check if the submit button has been clicked
        if(isset($_POST['send'])) {
            
            // Assign a default value if an input box hasn't been filled
            if(empty($_POST['firstname']))
                $_POST['firstname'] = 'noName';

            if(empty($_POST['lastname']))
                $_POST['lastname'] = 'noLastName';

            if(empty($_POST['gender']))
                $_POST['gender'] = 'M';

            if(empty($_POST['country']))
                $_POST['country'] = 'noCountry';

            if(empty($_POST['city']))
                $_POST['city'] = 'noCity';
            
            if(empty($_POST['birth_date']))
                $_POST['birthdate'] = NULL;

            // Array which contains all the database columns
            $column_values = array(
                ':first_name'=>$_POST['firstname'],
                ':last_name'=>$_POST['lastname'],
                ':email'=>$_POST['email'],
                ':username'=>$_POST['username'],
                ':password'=>$_POST['pwd'],
                ':gender'=>$_POST['gender'],
                ':country'=>$_POST['country'],
                ':city'=>$_POST['city'],
                ':birth_date'=>$_POST['birthdate']
            );
            print_r($column_values);

            // Send the values inserted into the input boxes into the database
            $sql_query = "INSERT INTO account_info ". 
            "(first_name, last_name, email, username, password, signup_date, gender, country, city, birth_date) ".
            "VALUES (:first_name, :last_name, :email, :username, :password, NOW(), :gender, :country, :city, :birth_date)";
            
            // Prepared Statement to prevent SQL Injection
            $preparedStatement = $dbConnection->prepare($sql_query);
            
            if($preparedStatement->execute($column_values))
                echo "Query sent successfully to the Database";
            else
               echo "Something went wrong during the query send to the Database";
               
        }

        // Close the connection to the database 
        $dbConnection = null;


    } catch(PDOException $e) {
        
        die("Connection failed: " . $e->getMessage() . "<br>");

    }
?>
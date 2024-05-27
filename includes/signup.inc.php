<?php

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = $_POST["username"];
    $pwd = $_POST["pwd"];
    $pwd2 = $_POST["pwd2"];
    $email = $_POST["email"];

    try{

        require_once 'dbh.inc.php';
        require_once 'signup_model.inc.php';
        require_once 'signup_contr.inc.php';

        //ERROR HANDLERS

        $errors = []; //We will add every error to this array

        if (is_input_empty($username, $pwd, $email)) {
            $errors["empty_input"] = "Fill in all fields!";
        }
        
        if (is_email_invalid($email)){
            $errors["invalid_email"] = "Please submit a valid e-mail!";
        }

        if (is_username_taken($pdo, $username)){
            $errors["username_taken"] = "This username is already taken!";
        }

        if (is_email_registered($pdo, $email)){
            $errors["email_registered"] = "This e-mail is already registered!";              
        }

        if (is_password_diff($pdo, $pwd, $pwd2)){
            $errors["password_diff"] = "The passwords are different!";              
        }

        require_once 'config_session.inc.php';

        if ($errors) {
            $_SESSION["errors_signup"] = $errors;

            $signupData = [
                    "username" => $username,
                    "email" => $email,

            ];
            $_SESSION["signup_data"] = $signupData;

            header("Location: ../index.php");
            die();
        }

        create_user($pdo, $username, $pwd, $email);

        header("Location: ../index.php?signup=success");

        $pdo = null;
        $stmt = null;

        die();
        

    } catch (PDOException $e) {
        die("Query failed: " . $e->getMessage());
    }

} else {
    header("Location: ../index.php");
    die();
}
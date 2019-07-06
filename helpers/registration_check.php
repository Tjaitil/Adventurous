<?php
     function registration_check($conn, $post_array) {
        $errors = array();
        if (strlen(trim($_POST['username'])) < 2 ) {
           $errors['userErr'] = "Your username must contain atleast 2 characters!"; 
        }
        else {
            $sql = "SELECT email, id FROM users WHERE username=:username";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = trim($post_array['username']);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if($stmt->rowCount() > 0) {
                $errors['usernameErr'] = "This username is already taken!";
                if($row['email'] == $post_array['email']) {
                    $errors['emailErr'] = "This mail is already taken!";
                }
            }
        }
        if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['emailErr'] = "The mail you entered is not valid!";
        }
        if (strlen(trim($_POST['password'])) < 8) {
            $errors['passwordErr'] = "Your password must be at least 8 characters!";
        }
        if (empty($errors['passErr']) && $post_array['password'] != $post_array['confirm_password']) {
            $errors['password_confirmErr'] = "The password doesnt match!";
        }
        $dob = $post_array['dob'];
        /*$dob = strtotime($dob);*/
        if($dob == NULL) {
            $errors['dobErr'] = "Please insert a valid date";
        }
        else {
            $date = explode("-", $dob);
            var_dump($date);
            /*list($y, $m, $d) = explode("-", $dob);*/
            if (checkdate($date[1], $date[2], $date[0]) == false || intval($date[0]) > 2005) {
                echo $date[0];
                $errors['dobErr'] = "You must be over 13 years old to play!";
            }
        }
        if(!isset($post_array['checkbox'])) {
            $errors['checkboxErr'] = "You must accept terms and agreement";
        }
        return $errors;
     }
?>
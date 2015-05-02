<?php

// GET http://www.yourwebsite.com/api/users
function getUsers() {
    $sql = "SELECT user_id,username,name,profile_pic FROM users ORDER BY user_id DESC";
    try {
        $db = getDB();
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        echo '{"users": ' . json_encode($users) . '}';
    } catch(PDOException $e) {
        //error_log($e->getMessage(), 3, '/var/tmp/phperror.log'); //Write error log
        echo '{"error":{"message":'. $e->getMessage() .'}}';
    }
}

// GET http://www.yourwebsite.com/api/users/search/sri
function getUserSearch($query) {
    $sql = "SELECT user_id,username,name,profile_pic FROM users WHERE username LIKE ? ORDER BY user_id DESC";
    try {
        $db = getDB();
        $stmt = $db->prepare($sql);
        $stmt->execute(array("%$query%"));
        $users = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        echo '{"users": ' . json_encode($users) . '}';
    } catch(PDOException $e) {
        //error_log($e->getMessage(), 3, '/var/tmp/phperror.log'); //Write error log
        echo '{"error":{"message":'. $e->getMessage() .'}}';
    }
}

function login () {

    $sql = "SELECT username, password FROM users WHERE username = ?";
    try {
        $body = getBody();
        $db = getDB();
        $stmt = $db->prepare($sql);
        $stmt->execute(array($body->username));
        $users = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;

        if(sizeof($users) > 0) {
            $user = reset($users);
            $salt = substr($user->password, 0, 10);
            $password = substr($user->password, 10);

            if($password == base64_encode(hash('sha256', $salt . $body->password))) {
                echo '{"success": true}';
            } else {
                echo '{"success": false, "message": "Wrong password."}';
            }

        } else {
            echo '{"success": false, "message": "Username not found."}';
        }

    } catch(PDOException $e) {
        //error_log($e->getMessage(), 3, '/var/tmp/phperror.log'); //Write error log
        echo '{"error":{"message":'. $e->getMessage() .'}}';
    }
}

function register() {
    $salt = substr(base64_encode(uniqid(mt_rand(), true)),10, 10);
    $password = $salt . base64_encode(hash('sha256', $salt . getBody()->password));
}
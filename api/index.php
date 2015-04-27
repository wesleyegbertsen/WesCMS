<?php
session_cache_limiter(false);
session_start();
require 'db.php';
require 'Slim/Slim.php';
\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();

//For application/json
$body = json_decode($app->request->getBody());

/* ROUTING */
$app->get('/users','getUsers');
$app->get('/updates','getUserUpdates');
$app->post('/updates', 'insertUpdate');
$app->delete('/updates/:update_id','deleteUpdate');
$app->get('/users/search/:query','getUserSearch');
$app->get('/settings','getSettings');
$app->get('/settings/:value','getSetting');

/* Slim Framework settings */
$app->contentType('application/json');
$app->notFound(function () {
    echo '{"error":{"text":"404 Page Not Found"}}';
});

$app->run();

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
		echo '{"error":{"text":'. $e->getMessage() .'}}';
	}
}

// GET http://www.yourwebsite.com/api/updates
function getUserUpdates() {
	$sql = "SELECT A.user_id, A.username, A.name, A.profile_pic, B.update_id, B.user_update, B.created FROM users A, updates B WHERE A.user_id=B.user_id_fk  ORDER BY B.update_id DESC";
	try {
		$db = getDB();
		$stmt = $db->prepare($sql);
		$stmt->execute();  
		$updates = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		echo '{"updates": ' . json_encode($updates) . '}';
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}';
	}
}

// DELETE http://www.yourwebsite.com/api/updates/delete/10
function deleteUpdate($update_id)
{
	$sql = "DELETE FROM updates WHERE update_id = ?";
	try {
        $db = getDB();
        $stmt = $db->prepare($sql);
        $stmt->execute(array($update_id));
        $db = null;
		echo true;
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}';
	}
}

// POST http://www.yourwebsite.com/api/updates
function insertUpdate(){
    echo getBody();
}

function getUserUpdate($update_id) {
//.....
//.....
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
		echo '{"error":{"text":'. $e->getMessage() .'}}';
	}
}

function getSettings () {
    $sql = "SELECT setting_name,setting_value FROM settings";
    try {
        $db = getDB();
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $settings = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        echo '{"settings": ' . json_encode($settings) . '}';
    } catch(PDOException $e) {
        //error_log($e->getMessage(), 3, '/var/tmp/phperror.log'); //Write error log
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

function getSetting($value) {
    $sql = "SELECT setting_value FROM settings WHERE setting_name = ?";
    try {
        $db = getDB();
        $stmt = $db->prepare($sql);
        $stmt->execute(array($value));
        $setting = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        if(sizeof($setting) > 0) {
            echo '{"setting":"' . $setting[0]->setting_value . '"}';
        } else {
            echo '{"error":{"text":"Setting not found"}}';
        }
    } catch(PDOException $e) {
        //error_log($e->getMessage(), 3, '/var/tmp/phperror.log'); //Write error log
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

function getBody() {
    global $body;
    return $body;
}
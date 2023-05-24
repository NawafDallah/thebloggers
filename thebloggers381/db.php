<?php
function connect() {
    $username = 'root';
    $password = '';
    $mysqlhost = 'localhost';
    $dbname = 'test';
    $pdo = new PDO('mysql:host='.$mysqlhost.';dbname='.$dbname.';charset=utf8', $username, $password);
     if($pdo){
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_ORACLE_NULLS, PDO::NULL_NATURAL);
        return $pdo;
    }else{
        die("Could not create PDO connection.");
    }
}

$sql = connect();
///
?>
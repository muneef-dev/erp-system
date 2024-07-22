<?php
$con = new mysqli('localhost','root','1234','assignment');

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}
?>

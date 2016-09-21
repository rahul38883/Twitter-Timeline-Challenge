<?php
session_start();
if(!isset($_SESSION['logged_in'])) header('Location: /index.php');
if(empty($_SESSION['logged_in'])) header('Location: /index.php');
if($_SESSION['logged_in'] != 1) header('Location: /index.php');
?>
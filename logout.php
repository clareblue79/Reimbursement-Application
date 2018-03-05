<!-- CS 304: Final Project
File Name: logout.php
Team Name: CHILY
Programmers: Clare Lee, Hanae Yaskawa
Last Modified Date: 05/10/2017 

Logs out the user -->

    
<?php 
 session_start();
 if (!isset($_SESSION['user'])) {
  header("Location: index.php");
 } else if(isset($_SESSION['user'])!="") {
  header("Location: accountHome.php");
 }
 
 if (isset($_GET['logout'])) {
  unset($_SESSION['user']);
  session_unset();
  session_destroy();
  header("Location: index.php");
  exit;
 }

?>



<!-- CS 304: Final Project
File Name: setup.php
Team Name: CHILY
Programmers: Clare Lee, Hanae Yaskawa
Last Modified Date: 05/10/2017 

This php function sets up the connetion to the database and ensures the
pages are redirected to https 
-->


<?php 

require_once("/home/cs304/public_html/php/DB-functions.php");
require_once("chily-dsn.inc");
      
if($_SERVER["HTTPS"] != "on") { //if not HTTPS redirect to https
    header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
    exit();
}
    
   
try{//conects to our database
    
$chily_dsn['database'] = 'chily_db';
$conn = db_connect($chily_dsn);
echo "<script>console.log( 'Connected to chily_db...' );</script>";

} catch (PDOException  $e){
    
echo "<script>console.log( 'Database Connection Error: $e');</script>";

}
//END FILE
?>
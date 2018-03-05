<?php
/*
CS 304 Final Project: Wellesley Reimbursement Website
Team name: CHiLY
Programmers: Clare Lee, Hanae Yaskawa
File: checkReimbBnum.php
Description: Called by javascript from form.js that passes in a reimb_bnumber. 
This php function then looks in chily_db database to return the count of rows 
in accts table where the bnumber matches up (should be 1 or 0 since each bnumber
is unique), fullname (if bnumber exists) and address (if bnumber exists).
*/

require_once("/home/cs304/public_html/php/DB-functions.php");
require_once('chily-dsn.inc');

// if a bnumber is passed in
if (isset($_POST["bnum"])) {

   // sets up database connection
   try{
	$chily_dsn['database'] = 'chily_db';
   	$conn = db_connect($chily_dsn);
   	
   } catch (PDOException  $e){
        echo "<script>console.log('checkReimbBnumDatabase Connection Error: $e');</script>";
   }
   
   $searchBnum = htmlspecialchars($_POST["bnum"]);

   try {
      // sets up sql to search for bnumber
      $searchsql = "SELECT count(*) AS count, fullname, address FROM accts WHERE bnumber=?";
   
      $resultsql = prepared_query($conn, $searchsql, array($searchBnum));
   
      // returns entire row of result
      while ($row = $resultsql->fetchRow(MDB2_FETCHMODE_ASSOC)) {
         echo json_encode($row);
      }
      
   } catch (PDOException  $e) {
      echo ("<script>console.log('checkReimbBnum.php error: $e');</script>");
   }
}


?>
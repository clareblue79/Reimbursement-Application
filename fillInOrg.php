<?php
/*
CS 304 Final Project: Wellesley Reimbursement Website
Team name: CHiLY
Programmers: Clare Lee, Hanae Yaskawa
File: fillInOrg.php
Description: Called by javascript on formTemplate.php that passes in an orgid. This php function then looks in chily_db database to return corresponding bookkeeper's name and three FOAPAL numbers in an associative array.
*/

require_once("/home/cs304/public_html/php/DB-functions.php");
require_once('chily-dsn.inc');

if (isset($_POST["orgid"])) {

   // org selected by user via dropdown menu, values of orgs are orgid 
   // generated via selecting orgids from database: no XSS attack vulnerability
   $orgid = $_POST["orgid"];
  
   try{
	$chily_dsn['database'] = 'chily_db';
   	$conn = db_connect($chily_dsn);
   	
   } catch (PDOException  $e){
        echo "console.log( 'Database Connection Error: $e');";
   }

   // query to find unique bookie name from orgid 
   $bookiesql = "SELECT fullname FROM orgs, accts WHERE orgs.orgid=$orgid AND orgs.bookieid=accts.uid AND acct_type='Bookkeeper'";
   
   $bresult = query($conn, $bookiesql);
   
   // sets up result associative array
   $orgDict = array();
   
   while ($row = $bresult->fetchRow(MDB2_FETCHMODE_ASSOC)){
   	 $orgDict['bookie'] = $row["fullname"];
   }

   // query to find the three FOAPAL numbers unique to org
   $foapalsql = "SELECT sofc, profits, clce FROM orgs WHERE orgid=$orgid";
   $fresult = query($conn, $foapalsql);
   
   while ($row = $fresult->fetchRow(MDB2_FETCHMODE_ASSOC)){
   	 $orgDict['sofc'] = $row["sofc"];
	 $orgDict['profits'] = $row["profits"];
	 $orgDict['clce'] = $row["clce"];
   }
   
   echo json_encode($orgDict);
}
?>
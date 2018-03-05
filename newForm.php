<!-- CS 304 Final Project: Wellesley Reimbursement Website
Team name: CHiLY 
Programmers: Clare Lee, Hanae Yaskawa  
File: newForm.php
Description: Child of formTemplate.php: displays a new reimbursement request form
with blank values for the treasurer to fill out.
-->

<?php

// gets connection to database and access to db functions
require_once("setup.php");
// gets file that contains helper functions
require_once("formFunc.php");



// sets up dropdown menu selection of org names
// starts with "Choose Organization" label
$orgOptions = '<option value="" disabled selected>Choose Organization</option>';

// uses helper function in formFunc.php to get a dictionary of all orgs
$orgDict = getAllOrgs($conn);

// loops through each org to add orgname to list of org options
foreach ($orgDict as $orgid => $orgname) {
   $orgOptions .= "<option value='$orgid'>$orgname</option>";
}


// sets bookie input to null
$bookieVal = "";

// sets reimbursement person's name, address, bnumber and bnumber_error message
// inputs to null
$reimb_bnumVal = "";
$reimb_nameVal = "";
$reimb_addressVal = "";


// for events table, sets first empty row
$eventsVal = createFirstEventRow();

// sets number of initial number of rows in table of reimbursements to be one
$numEventsVal = 1;

// sets values in foapal table to be null
$sofc_foapalVal = "###";
$sofc_locVal = "";
$sofc_amntVal = "";
$profit_foapalVal = "###";
$profit_locVal = "";
$profit_amntVal = "";
$clce_foapalVal = "###";
$clce_locVal = "";
$clce_amntVal = "";
$ttl_amntVal = "";


// sets first receipt input and number of receipts
$receiptsVal = '<input type="file" name="receipt1">';
$numReceiptsVal = 1;


// sets first list of attendees input and number of lists
$attendeesVal = '<input type="file" class="listattendees" name="attendees1">';
$numAttendeesVal = 1;


// sets special instructions radio buttons to be unchecked (aka null)
$spec_instSend = "";
$spec_instEmail = "";
$spec_instEmailVal = "";


// requires template of form
require_once("formTemplate.php");


// when submit button is clicked
if (isset($_POST["submit_btn"])) {
   echo ("<script>console.log('The submit button has been clicked...');</script>");
   
   // inserts form using helper function that returns this new form's fid
   $newFid = insertForms($conn);
   
   // inserts events into events table using helper function
   insertEvents($conn, $newFid);
   
   // checks to see if there are any special instructions 
   // if so, adds special instructions to forms table
   checkSpecInst($conn, $newFid);
   
   // checks to see whether there are any files to upload
   // if so, uploads files 
   checkUploads($conn, $newFid);
   
   // sends form to bookie_approval table
   sendToBookie($conn, $newFid);

   // when form is submitted and all information is stored in database,
   // redirects browser to Treasurer's formPage
   header("Location: formPage.php");
   exit();
   
      
}




?>
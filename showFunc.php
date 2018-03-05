<?php
/*
CS 304 Final Project: Wellesley Reimbursement Website
Team name: CHiLY
Programmers: Clare Lee, Hanae Yaskawa
File: showFunc.php

Description: Contains function called by form.php to insert data from form
into database when submit button is clicked.
*/

require_once('formListFunctions.php'); //to use get username function

// ----------
// Function takes in db connection and orgid, returns row that is result
// of query that searches for all details about the org
function getOrgInfo($conn, $orgid) {
   $osql = "SELECT * FROM orgs WHERE orgid=$orgid";
   $oresult = query($conn, $osql);
   $row = $oresult->fetchRow(MDB2_FETCHMODE_ASSOC);
   return $row;
}

// ----------
// Function takes in db connection and formid, returns row that is result
// of query that searches for all details about the form
function getFormInfo($conn, $fid) {
   $fsql = "SELECT * FROM forms WHERE fid=$fid";
   $fresult = query($conn, $fsql);
   $row = $fresult->fetchRow(MDB2_FETCHMODE_ASSOC);
   return $row;
}


// ----------
// Function takes in db connection and formid, 
// searches for all details about all the events associated with the form
// and echoes a row of the event data that is added in the table of events
function getEvents($conn, $fid) {
   $esql = "SELECT * FROM events WHERE fid=$fid";
   $eresult = query($conn, $esql);

   // creates new table row for each event
   while ($row = $eresult->fetchRow(MDB2_FETCHMODE_ASSOC)) {
      $newRow = "<tr>";
      $newRow .= "<td>".$row["ename"]."</td>";
      $newRow .= "<td>".$row["edate"]."</td>";
      $newRow .= "<td>".$row["num_attendees"]."</td>";
      $newRow .= "<td>".$row["category"]."</td>";
      $newRow .= "<td>".$row["amnt"]."</td>";
      $newRow .= "<td>".$row["fundsrc"]."</td>";
      $newRow .= "</tr>";
      echo ($newRow);
   }
}


// ----------
// Function takes in db connection, formid and orgid,
// searches for FOAPAL information and location/amount information
// and echoes the rows of data that are added in the FOAPAL table
function getFoapal($conn, $fid, $orgid) {

   // uses helper function to get foapal numbers using orgid
   $orgInfo = getOrgInfo($conn, $orgid);
   $sofc = $orgInfo["sofc"];
   $profits = $orgInfo["profits"];
   $clce = $orgInfo["clce"];

   // uses helper function to get location and amount information
   $formInfo = getFormInfo($conn, $fid);
   $sofc_amnt = $formInfo["sofc_amnt"];
   $sofc_loc = $formInfo["sofc_loc"];
   $profit_amnt = $formInfo["profit_amnt"];
   $profit_loc = $formInfo["profit_loc"];
   $clce_amnt = $formInfo["clce_amnt"];
   $clce_loc = $formInfo["clce_loc"];
   $ttl_amnt = $formInfo["ttl_amnt"];   

   $sofcRow = "<tr><td>81".$sofc."</td><td>4610</td>";
   $sofcRow .= "<td>7998</td><td>981</td><td>SOFC</td>";
   $sofcRow .= "<td>".$sofc_loc."</td><td>".$sofc_amnt."</td>";

   $profitsRow = "<tr><td>83".$profits."</td><td>4620</td>";
   $profitsRow .= "<td>7998</td><td>982</td><td>PROFITS</td>";
   $profitsRow .= "<td>".$profit_loc."</td><td>".$profit_amnt."</td>";

   $clceRow = "<tr><td>83".$clce."</td><td>4620</td>";
   $clceRow .= "<td>7999</td><td>982</td><td>CLCE</td>";
   $clceRow .= "<td>".$clce_loc."</td><td>".$clce_amnt."</td>";

   $ttlRow = "<tr><td></td><td></td><td></td><td></td>";
   $ttlRow .= "<td></td><td>TOTAL AMOUNT</td><td>".$ttl_amnt."</td></tr>";

   echo ($sofcRow.$profitsRow.$clceRow.$ttlRow);
}


// ----------
// Function that takes in db connection and formid 
// searches to see whether special instructions exist
// if so, displays special instructions
function getSpecInst($conn, $fid) {
   echo ("<script>console.log('Calling getSpecInstr...');</script>"); 
   
   // uses helper function to get forminfo
   $spec_inst = getFormInfo($conn, $fid)["spec_inst"];
   $email = getFormInfo($conn, $fid)["email"];

   if ($spec_inst != NULL) {
      echo ("<script>console.log( 'There are special instructions' );</script>");
      echo ("<h3>Special Instructions</h3>");
      
      if ($spec_inst == "send_check") {
         echo ("<p>Send check to address listed above.<p>");
      
      } elseif ($spec_inst == "email_check") {
          $emailInst = "<p>Person to e-mail when check is ready for pick up at the \
Cashier's Window in Green Hall: ";
	  $emailInst .= $email."</p>";
	  echo $emailInst;
      }
   } else {

      echo ("<script>console.log( 'There are no special instructions' );</script>");
   }	  
}


// ----------
// Function that takes in db connection and formid
// searches for the receipts associated with formid
// displays the receipts
function getReceipts($conn, $fid) {
   $rsql = "SELECT rfile FROM receipts WHERE fid=$fid";
   $rresult = query($conn, $rsql);
   try {
      while ($row=$rresult->fetchRow(MDB2_FETCHMODE_ASSOC)) {
         $filename = $row["rfile"];
         echo ("<p><img src='./$filename'></p>");
      }
   } catch (PDOException  $e) {
      echo ("<script>console.log('Database search error: $e');</script>");
   }
}


// ----------
// Function that takes in db connection and formid
// searches for the lists of attendees associated with formid
// displays the lists of attendees
function getAttendees($conn, $fid) {
   $asql = "SELECT afile FROM attendees WHERE fid=$fid";
   $aresult = query($conn, $asql);
   try {
      while ($row=$aresult->fetchRow(MDB2_FETCHMODE_ASSOC)) {
         $filename = $row["afile"];
         echo ("<p><img src='./$filename'></p>");
      }
   } catch (PDOException  $e) {
      echo ("<script>console.log('Database search error: $e');</script>");
   }
}


// ----------
// Function that takes in db connection and formid
// changes status of this form in bookie_approval table to "approved"
function bookieApproves($conn, $fid) {
   // Gets current date when bookie clicks approval button
   $approveDate = date('Y-m-d');
   echo "<script>console.log('$approveDate');</script>";

   $comments = htmlspecialchars($_POST["comments"]);
   
   try {

      $sql = "UPDATE bookie_approval SET status='approved', approved_date=?, comment=? WHERE fid=?";
      $result = prepared_query($conn, $sql, array($approveDate,$comments, $fid));
   
   } catch (PDOException  $e){
      echo "<script>console.log( 'Database Insertion Error: $e');</script>";
   }
}


// ----------
// Function that takes in db connection and formid
// "sends" this form to bursar for approval by
// inserting this form into bursar_approval table
function sendToBursar($conn, $fid) {
   // Gets current date when bookie "sends" form to bursar
   $sendDate = date('Y-m-d');
   $sql = "INSERT INTO bursar_approval(fid, status, approved_date) VALUES (?, ?, ?)";
   $notchecked = "not_checked";
   $result = prepared_query($conn, $sql, array($fid, $notchecked, $sendDate));
}


// ----------
// Function that takes in db connection and formid
// changes status of form in bookie_approval table to "rejected"
function bookieRejects($conn, $fid) {
   // Gets current date	when bookie clicks reject button
   $rejectDate = date('Y-m-d');
   $comments = htmlspecialchars($_POST["comments"]);

   try {
      $sql = "UPDATE bookie_approval SET status='rejected', approved_date= ?, comment=? WHERE fid=?";
      $result = prepared_query($conn, $sql, array($rejectDate,$comments, $fid));
   } catch (PDOException  $e){
      echo "<script>console.log( 'Database Insertion Error: $e');</script>";
   }
}

// ----------
// Function that takes in db connection and formid
// changes status of this form in bursar_approval table to "approved"
function bursarApproves($conn, $fid) {
   // Gets current date when bursar clicks approval button
   $approveDate = date('Y-m-d');
   echo "<script>console.log($approveDate);</script>";

   $comments = htmlspecialchars($_POST["comments"]);
   
   try {

      $sql = "UPDATE bursar_approval SET status='approved', approved_date=?, comment=? WHERE fid=?";
      $result = prepared_query($conn, $sql, array($approveDate,$comments, $fid));
   
   } catch (PDOException  $e){
      echo "<script>console.log( 'Database Insertion Error: $e');</script>";
   }
}


// ----------
// Function that takes in db connection and formid
// changes status of form in bursar_approval table to "rejected"
function bursarRejects($conn, $fid) {
   // Gets current date	when bookie clicks reject button
   $rejectDate = date('Y-m-d');
   $comments = htmlspecialchars($_POST["comments"]);

   try {
      $sql = "UPDATE bursar_approval SET status='rejected', approved_date=?, comment=? WHERE fid=?";
      $result = prepared_query($conn, $sql, array($rejectDate, $comments, $fid));
       
   } catch (PDOException  $e){
      echo "<script>console.log( 'Database Insertion Error: $e');</script>";
   }
}

// ----------
// Function that takes in db connection, uid of the reimbursed person (person to send email to),
// and the uid of current bursar (person to send email from) and sends an email notification 
//that the reimbursement form has been approved by the Student Bursar and it is in the controller's office
function sendEmailToReimbPerson($conn, $uidTo, $fid){
    $to = getUsername($conn, $uidTo);
    $name = getFullname($conn, $uidTo);
    $subject = "Reimbursement Form has been approved";
    $message = "Dear $name,
    \nYour reimbursement form has been approved by the Student Bursar. 
    \nFor more information, please go to the Wellesley Reimbursment Application or contact your treasurer. 
    \nPlease do not reply to this message.
    \nThank you for using our application. 
    \nBest Regards, 
    \nThe Wellesley Reimbursment Application";

    // Send
    mail($to, $subject, $message);  
}



?>
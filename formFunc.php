<?php
/*
CS 304 Final Project: Wellesley Reimbursement Website
Team name: CHiLY
Programmers: Clare Lee, Hanae Yaskawa
File: formFunc.php

Description: Contains functions called by editForm.php and showForm.php 
to get information FROM database.
*/


// ----------
// Function gets all data from form and enters into forms table in chily_db
// Called on a new form that has not previously been saved and does not 
// have a FID

function insertForms($conn){
   $uid = $_SESSION['user'];

   // org selected by user via dropdown	menu, values of	orgs are orgid
   // generated via selecting orgids from database: no XSS attack vulnerability
   $orgid = $_POST['orgs'];

   // auto-inputted via	php to get current date: no XSS	attack vulnerability
   $date_prepared = $_POST['date_prepared'];

   // gets reimbursement person's bnumber 
   $reimb_bnum = htmlspecialchars($_POST['reimb_bnum']);
   //echo ("<script>console.log('$reimb_bnum');</script>");
   // uses helper function to get userid from person's bnumber
   $reimb_id = getUserId($conn, $reimb_bnum);
  // echo ("<script>console.log('$reimb_id');</script>");
   
   
   $purpose = htmlspecialchars($_POST['purpose']);
   $sofc_amnt = htmlspecialchars($_POST['sofc_amnt']);
   $sofc_loc = htmlspecialchars($_POST['sofc_loc']);
   $profit_amnt = htmlspecialchars($_POST['profit_amnt']);
   $profit_loc = htmlspecialchars($_POST['profit_loc']);
   $clce_amnt = htmlspecialchars($_POST['clce_amnt']);
   $clce_loc = htmlspecialchars($_POST['clce_loc']);
   $ttl_amnt = $sofc_amnt+$profit_amnt+$clce_amnt;
   echo ("<script>console.log('Total: $ttl_amnt');</script>");
   $status = 'submitted';

   try{

      $insertsql = "INSERT INTO forms(uid, orgid, date_prepared, reimb_id, purpose, sofc_amnt, sofc_loc, profit_amnt, profit_loc, clce_amnt, clce_loc, ttl_amnt, status) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?)";

      $resultset = prepared_query($conn, $insertsql, array($uid, $orgid, $date_prepared,  $reimb_id, $purpose, $sofc_amnt, $sofc_loc, $profit_amnt, $profit_loc, $clce_amnt, $clce_loc, $ttl_amnt, $status));

   } catch (PDOException  $e){
      echo ("<script>console.log('insertForms Insertion Error: $e');</script>");
   }

   echo ("<script>console.log('Form submitted');</script>");
   
   // Gets this form's formid 
   $fid = $conn->lastInsertId();
   
   return $fid;
}


// ----------
// Function checks to see whether there are special instructions and 
// enters data into forms table in chily_db
function checkSpecInst($conn, $fid) {

   if (isset($_POST['spec_inst'])) {

      // values for spec_inst were predetermined so no XSS attack vulnerability
      $spec_inst = $_POST['spec_inst'];

      echo ("<script>console.log('Check spec_inst: $spec_inst');</script>");

      // sets up sql to insert spec_inst for send_check: no email necessary
      if ($spec_inst == 'send_check') {

         $sql = "UPDATE forms SET spec_inst='$spec_inst' WHERE fid=$fid";

      }
      
      // sets up sql to insert spec_inst for email: email necessary
      elseif ($spec_inst == 'email_check') {

         $email = htmlspecialchars($_POST['email']);
	 $sql = "UPDATE forms SET spec_inst='$spec_inst', email='$email' WHERE fid=$fid";
      }

      // executes query to update special instructions in forms table
      try {
         $result = query($conn, $sql);

      } catch (PDOException  $e){
         echo ("<script>console.log('Database Update Error: $e');</script>");
      }
   }

   else {
      echo ("<script>console.log('Check spec_inst: none');</script>");
   }

}


// ----------
// Function gets all the events in the table of reimbursement items and
// enters data into events table in chily_db 

function insertEvents($conn, $fid) {
   echo ("<script>console.log('Calling insertEvents...');</script>"); 
   // Gets number of items in events table
   $n = $_POST['numEvents'];
   $orgid = $_POST['orgs'];
   echo ("<script>console.log('numEvents: $n');</script>");
   
   for ($i=1; $i<=$n; $i++){
      $ename = htmlspecialchars($_POST['event'.$i]);
      $edate = htmlspecialchars($_POST['edate'.$i]);
      $num_attendees = htmlspecialchars($_POST['num_attendees'.$i]);
      $category = htmlspecialchars($_POST['category'.$i]);
      $amnt = htmlspecialchars($_POST['amnt'.$i]);
      $fundsrc = htmlspecialchars($_POST['fundsrc'.$i]);
      
      try {
         $esql = "INSERT INTO events(ename, fid, orgid, edate, num_attendees, category, amnt, fundsrc) VALUES (?,?,?,?,?,?,?,?)";
         $eresult = prepared_query($conn, $esql, array($ename, $fid, $orgid, $edate, $num_attendees, $category, $amnt, $fundsrc));
      } catch (PDOException $e) {
         echo ("<script>console.log('insertEvents Error: $e');</script>");
      }
   }
}


// ----------
// Function "sends" submitted form to bookie_approval table

function sendToBookie($conn, $fid) {
   echo ("<script>console.log('calling send to bookie');</script>");
   // gets bookie's id using search query by searching bookie's name
   $orgid = $_POST['orgs'];
   $bookieSql = "select bookieid from orgs where orgid=$orgid";

   $result = query($conn, $bookieSql);
   $row = $result->fetchRow(MDB2_FETCHMODE_ASSOC);

   $bookieid = $row["bookieid"];

   // gets date that treasurer submits form to send to bookie for approval
   $date_prepared = $_POST["date_prepared"];
   echo ("<script>console.log( 'Sending to bookie on $date_prepared');</script>");   
      
   // inserts form into bookie_approval table
   $insertSql = "INSERT INTO bookie_approval(fid, bookieid, status, approved_date) VALUES ($fid, $bookieid, 'not_checked', '$date_prepared')";
   $conn->exec($insertSql);   
}

// ----------
// Function takes in db connection, 
// returns all orgs in database as a dictionary with orgid as key 
// and orgname as value
function getAllOrgs($conn) {
   // sets up empty dictionary
   $orgDict = array();
   
   // looks up all orgnames and orgids from database
   $orgsql = "SELECT orgid, orgname FROM orgs";
   $allorgs = query($conn, $orgsql);
   
   // adds each org to associative array
   while ($row = $allorgs->fetchRow(MDB2_FETCHMODE_ASSOC)){
      $orgid = $row['orgid'];
      $orgname = $row['orgname'];
      $orgDict[$orgid] = $orgname;
   }
   return $orgDict; 
}


// ----------
// Function creates the first empty row for events table 
// returns new row as a string that is used in editForm.php and newForm.php
// as the value of $eventsVal
function createFirstEventRow() {
   $eventsVal = '<tr id="row1">';
   $eventsVal .= '<td><input type="text" name="event1" placeholder="Event name" required></td>';
   $eventsVal .= '<td><input type="date" name="edate1" required></td>';
   $eventsVal .= '<td><input type="number" name="num_attendees1" placeholder="Number of attendees" min="1" step="1" required></td>';
   $eventsVal .= '<td><input type="text" name="category1" placeholder="Category" required></td>';
   $eventsVal .= '<td><input type="number" name="amnt1" step="0.01" min="0" placeholder="Amount" required></td>';
   $eventsVal .= '<td><select name="fundsrc1" required>';
   $eventsVal .= '<option value="" disabled selected>Select Source</option>';
   $eventsVal .= '<option value="Profits">Profits</option>';
   $eventsVal .= '<option value="SOFC">SOFC Deadline</option>';
   $eventsVal .= '<option value="GP">GP Org</option>';
   $eventsVal .= '<option value="CLCE">CLCE</option>';
   $eventsVal .= '</select></td></tr>';
   return $eventsVal;
}


// ----------
// Function takes in db connection and fid,
// searches for all events with fid,
// creates rows in events table for each event that are editable
// returns associative array of $eventsVal (events table rows as a string 
// that is used in editForm.php) and number of events
function getEventsEditable($conn, $fid) {
   // sets up empty string
   $eventsVal = "";
   
   // initiates counter for event number
   $n = 0;
   
   // looks up all events with fid
   $esql = "SELECT * FROM events WHERE fid=$fid";
   $eresult = query($conn, $esql);

   // creates new editable table row for each event and adds to result string
   while ($row = $eresult->fetchRow(MDB2_FETCHMODE_ASSOC)) {
      // increments event number counter
      $n += 1;
      
      // creates event name cell with saved value and adds to string
      $eName = 'event'.$n;
      $eNameVal = $row['ename'];
      $eventsVal .= '<tr><td><input type="text" name="$eName" placeholder="Event name" value="$eNameVal"></td>';
      
      // creates event date cell with saved value and adds to string
      $eDate = 'date'.$n;
      $eDateVal = $row['edate'];
      $eventsVal .= '<td><input type="date" name="$eDate" value="eDateVal"></td>';
      
      // creates event num_attendees cell with saved value and adds to string
      $eNum = 'num_attendees'.$n;
      $eNumVal = $row['num_attendees'];
      $eventsVal .= '<td><input type="number" name="$eNum" value="$eNumVal" placeholder="Number of attendees" min="1" step="1"></td>';
      
      // creates event category cell with saved value and adds to string
      $eCat = 'category'.$n;
      $eCatVal = $row['category'];
      $eventsVal .= '<td><input type="text" name="eCat" value="eCatVal" placeholder="Category"></td>';
      
      // creates event amount cell with saved value and adds to string
      $eAmnt = 'amnt'.$n;
      $eAmntVal = $row['amnt'];
      $eventsVal .= '<td><input type="number" name="$eAmnt" value="eAmntVal" step="0.01" min="0" placeholder="Amount" required></td>';
      
      // creates event funding source cell with saved value and adds to string
      $eFundSrc = 'fundsrc'.$n;
      $eFundSrcVal = $row['fundsrc'];
      $eventsVal .= '<td><select name="fundsrc1">';
      
      // if Profits was the saved value, marks Profits as selected value of dropdown menu
      if ($eFundSrcVal == "Profits") {
         $eventsVal .= '<option value="Profits" selected>Profits</option>';
         $eventsVal .= '<option value="SOFC">SOFC Deadline</option>';
         $eventsVal .= '<option value="GP">GP Org</option>';
         $eventsVal .= '<option value="CLCE">CLCE</option>';

      // if SOFC was the saved value, marks SOFC as selected value of dropdown menu
      } elseif ($eFundSrcVal == "SOFC") {
         $eventsVal .= '<option value="Profits">Profits</option>';
         $eventsVal .= '<option value="SOFC" selected>SOFC Deadline</option>';
         $eventsVal .= '<option value="GP">GP Org</option>';
         $eventsVal .= '<option value="CLCE">CLCE</option>';
         
      // if GP was the saved value, marks GP as selected value of dropdown menu
      } elseif ($eFundSrcVal == "GP") {
         $eventsVal .= '<option value="Profits">Profits</option>';
         $eventsVal .= '<option value="SOFC">SOFC Deadline</option>';
         $eventsVal .= '<option value="GP" selected>GP Org</option>';
         $eventsVal .= '<option value="CLCE">CLCE</option>';

      // if CLCE was the saved value, marks CLCE as selected value of dropdown menu
      } elseif ($eFundSrcVal == "SOFC") {
         $eventsVal .= '<option value="Profits">Profits</option>';
         $eventsVal .= '<option value="SOFC">SOFC Deadline</option>';
         $eventsVal .= '<option value="GP">GP Org</option>';
         $eventsVal .= '<option value="CLCE" selected>CLCE</option>';
      }
       
      // closes funding source dropdown menu and table row  
      $eventsVal .= '</select></td></tr>';
   }
   
   // if there were no saved events, and counter remains 0
   if ($n == 0) {
   
      // calls helper function to create a new empty row of events
      $eventsVal = createFirstEventRow();
      
      // increases count by one
      $n = 1;
   
   }
   
   $eventsDict = array();
   $eventsDict['eventsVal'] = $eventsVal;
   $eventsDict['numEventsVal'] = $n;
   
   return $eventsDict;
}



// ----------
// Function takes in db connection and fid,
// searches for any saved receipts,
// returns associative array of number of saved receipts and 
// string of saved receipt inputs (used as $receiptsVal in editForm.php
function getReceiptsEditable($conn, $fid) {
   // initiates result string
   $receiptsVal = "";
   
   // initiates receipt counter
   $n = 0;
   
   // sql to search for receipts
   $rsql = "SELECT rfile FROM receipts WHERE fid=$fid";
   $rresult = query($conn, $rsql);
   
   try {
      while ($row=$rresult->fetchRow(MDB2_FETCHMODE_ASSOC)) {
         // increments number of receipts
         $n += 1;
         
         // creates link to receipt
         $filename = $row["rfile"];
         $linkName = 'Receipt '.$n;
         $link = "<span><a href='./$filename'>$linkName</a></span>";
         
         // adds link to result string
         $receiptsVal .= $link;
      }
   } catch (PDOException  $e) {
      echo ("<script>console.log('Database search error: $e');</script>");
   }
   
   // if there were no saved receipts
   if ($n==0) {
   
      // adds empty input for a receipt
      $receiptsVal = '<input type="file" name="receipt1">';
      
      // increments number of receipts by one
      $n = 1;
   
   }
   
   $result = array();
   $result['receiptsVal'] = $receiptsVal;
   $result['numReceiptsVal'] = $n;
   
   return $result;

}



// ----------
// Function takes in db connection and uid, 
// returns reimbursement person's full name, address and bnumber
function getReimbPerson($conn, $uid) {
   $sql = "SELECT fullname, address, bnumber FROM accts WHERE uid=$uid";
   // uid is auto-incremented int generated from database so regular query ok
   $result = query($conn, $sql);
   try {
      $row = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
      echo $row;
   } catch (PDOException  $e) {
      echo ("<script>console.log('Database search error: $e');</script>");
   }
}


// ----------
// Function takes in db connection and bnumber of person to be reimbursed,
// returns userid
function getUserId($conn, $bnum) {
   echo("<script>console.log('called getUserId with: $bnum');</script>");
   
   $sql = "SELECT uid FROM accts WHERE bnumber=?";
   $result = prepared_query($conn, $sql, array($bnum)); 
   try {
      $row = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
      $uid =  $row['uid'];
      echo("<script>console.log('called getUserId: $uid');</script>");
      return $row['uid'];
   } catch (PDOException  $e) {
      echo ("<script>console.log('Database search UID error: $e');</script>");
   } 
}

// ----------
// Function takes in db connection and reimb_id (a uid in accts table)
// returns bnumber
function getBNum($conn, $reimb_id) {
   $sql = "SELECT bnumber FROM accts WHERE uid=$reimb_id";
   // uid is auto-incremented int generated from database so regular query ok
   $result = query($conn, $sql);
   try {
      $row = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
      echo $row['bnumber'];
   } catch (PDOException  $e) {
      echo ("<script>console.log('Database search BNUMBER error: $e');</script>");
   } 
}


// ----------
// Function gets orgid, given fid and connection to database
function getOrgid($conn, $fid) {
   $fsql = "SELECT orgid FROM forms WHERE fid=$fid";
   $fresult = query($conn, $fsql);
   $row = $fresult->fetchRow(MDB2_FETCHMODE_ASSOC);
   $orgid = $row['orgid']; 
   echo ("<script>console.log( 'Successfully got org $orgid' );</script>");
   return $orgid;
}


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
// Function takes in db connection and orgid, returns bookiename
function getBookie($conn, $orgid) {
   $bsql = "SELECT firstname FROM accts, orgs WHERE orgid = $orgid and bookieid=uid";
   $bresult = query($conn, $bsql);
   $row = $bresult->fetchRow(MDB2_FETCHMODE_ASSOC);
   return $row["fullname"];
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
// Function called to loop through receipts and list_attendees
// Calls uploadFile() function to upload each receipt or list of attendees
// Given FID, generates a unique filename in the form: 
// fid_receipt#.jpg or fid_attendees#.jpg

function checkUploads($conn, $fid){
   
   // loops through receipts
   $r = $_POST['numReceipts'];
   echo "<script>console.log('There are $r receipts.')</script>";

   for ($i=1; $i<=$r; $i++){
   
      // creates a unique filename fid_receipt#.jpg and uploads the receipt file
      $rname="receipt".$i;
      $filename="receipts/".$fid."_receipt".$i.".jpg";
      if (file_exists($_FILES[$rname]['tmp_name'])) {
         echo("<script>console.log('Created $filename for $rname')</script>");
      	 uploadFile($conn, $fid, "receipts", $filename, $rname);
      }
   }

   // loops through lists of attendees
   $a = $_POST['numAttendees'];
   echo("<script>console.log('There are $a lists of attendees.')</script>");

   for ($j=1; $j<=$a; $j++) {

      // creates a unique filename fid_attendees#.jpg and uploads file
      $aname="attendees".$j;
      $filename="attendees/".$fid."_attendees".$j.".jpg";
      if (file_exists($_FILES[$aname]['tmp_name'])) {
         echo("<script>console.log('Created $filename for $aname')</script>");
	 uploadFile($conn, $fid, "attendees", $filename, $aname);
      }
   }
}



// ----------
// Function called to upload one file and add its filename to receipts table 
// in database
// Takes connection to chily_db, formid, new filename (complete with directory
// path) and name of file input on html form

function uploadFile($conn, $fid, $dir, $destfile, $fname){

   echo("<script>console.log('Called function uploadFile...')</script>");

   if( $_FILES[$fname]['error'] != UPLOAD_ERR_OK ) {

      print "<P>Upload error: " . $_FILES[$fname]['error'];

   } else {

      // image was successfully uploaded
      $name = $_FILES[$fname]['name'];
      $type = $_FILES[$fname]['type'];
      $tmp  = $_FILES[$fname]['tmp_name'];

      // checks that file is really an image
      $mime = mime_content_type($tmp);

      if (($mime != 'image/jpeg')){
         die("<script>console.log('Error: not a jpeg image')</script>");
      }
      
      if( file_exists($destfile) ) {
      
         echo("<script>console.log('File $destfile already exists in $dir; overwriting it.')</script>");
         
         // both destination file name and fid were generated securely:
         // no XSS vulnerability
	     $query = "UPDATE receipts SET rfile='$destfile' WHERE fid='$fid'";

      } else {
         
         echo("<script>console.log('File $destfile does not exist in $dir; creating it.')</script>");

      if ($dir=="receipts") {
         echo("<script>console.log('Making query for receipts')</script>");
	     $query = "INSERT INTO receipts (rfile, fid) VALUES ('$destfile', '$fid')";

      } elseif ($dir=="attendees") {
	     echo("<script>console.log('Making query for attendees')</script>");
         $query = "INSERT INTO attendees (afile, fid) VALUES ('$destfile', '$fid')";
      }      
   }
 

      if (move_uploaded_file($tmp, $destfile)){

         echo("<script>console.log('File $tmp successfully moved; about to update database.')</script>");
         query($conn, $query);
         
      } else {
         echo ("<script>console.log('Error moving $tmp')</script>");
      }
   }
}



?>
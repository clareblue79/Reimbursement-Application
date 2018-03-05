<!-- CS 304 Final Project: Wellesley Reimbursement Website
Team name: CHiLY
Programmers: Clare Lee, Hanae Yaskawa
File: showForm.php
Description: The reimbursement form that has been submitted by a treasurer and is shown to the
bookie, student bursar, and controller's office for them to approve or reject.
ONLY bookie in charge of the form's org and Student bursar have access to this page.
* address format displaying different forms: showForm.php?fid=$fid 
-->

<!doctype html>
<html lang="en">


<?php
   // ID of current form
    if(isset($_REQUEST['fid'])){
        $fid = htmlspecialchars($_GET['fid']);
        $serv = $_SERVER['PHP_SELF'].'?fid='.$fid;
        echo "<script>console.log('Displaying info for fid=$fid at $serv');</script>";
    } else {
        echo "<script>console.log('Form with $fid does not exist. Redirecting to form review....');</script>";
        header("Location: formReview.php");
        exit();
    }
   
   ob_start();
   session_start();
   require_once("header.php");
   require_once("showFunc.php");
   require_once("setup.php");
   require_once("redirect.php");
   require_once("formListFunctions.php"); //to use bookieInCharge
    
   $testUser = $_SESSION['user'];
   $testAccount = $_SESSION['accountType'];

   $bookieInCharge = getBookieInCharge($conn, $fid);
    
   redirectFormReview($testUser, $bookieInCharge);
    
   // gets organization information using helper functions in showFunc.php
   $orgid = getOrgid($conn, $fid);
   $orgname = getOrgName($conn, $orgid);

   // gets bookie name
   $bookiename = getFullname($conn, $testUser);   
   // gets form information
   $formInfo = getFormInfo($conn, $fid);
    $reimbID = getReimbID($conn, $fid);
    $reimbName = getFullname($conn, $reimbID);
    $reimbBnum = getBnumber($conn, $reimbID);
    $reimbAdd = getAddress($conn, $reimbID);
    $reimbUsername = getUsername($conn, $reimbID);
    

   ?>


<body id="sofc_form">
<div id="request_form">
   <div class="header">
     <h1>SOFC Check Request Form</h1>
     <p>Please approve or reject the reimbursement request form.</p>
   </div>

   <div id='formDisplay'>
     <p>Organization: <?php echo($orgname) ?></p>
     <p>Bookkeeper: <?php echo($bookiename) ?></p>
     <p>Date Prepared: <?php echo($formInfo["date_prepared"]) ?></p>
       <br>
    <p>Person requesting reimbursement:</p>
        <?php 
        echo "<p>Username: $reimbUsername</p>";
        echo "<p>Full Name: $reimbName</p>";
        echo "<p>BNumber: $reimbBnum</p>";
        echo "<p>Address: $reimbAdd</p>";
         ?>
       <br>
     <p>Purpose: <?php echo($formInfo["purpose"]) ?></p>
   </div>
  
  <table id="list_reimb">
    <tr>
      <td>Event</td>
      <td>Date of Event</td>
      <td># of Student Attendees</td>
      <td>Category</td>
      <td>Amount</td>
      <td>Funding Source</td>
    </tr>
    <?php getEvents($conn, $fid) ?>
    </table>

   <h3>ACCOUNTING INFORMATION (FOAPAL #)</h3>
   <table id="list_foapal">
    <tr>
      <td>Fund (5)</td>
      <td>Org (4)</td>
      <td>Acct (4)</td>
      <td>Prgm (3)</td>
      <td>Activity (6)</td>
      <td>Location (3)</td>
      <td>Amount*</td>
    </tr>
    <?php getFoapal($conn, $fid, $orgid) ?>
   </table>

   <h3>Uploaded Receipts</h3>
  
   <?php getReceipts($conn, $fid) ?>
 
   <h3>Uploaded Lists of Attendees</h3>

   <?php getAttendees($conn, $fid) ?>
    
    <form action=<?php echo $serv;?> method=post>
      <h3>Comments:</h3><textarea rows="10" columns="100" name="comments"></textarea>
      <button type="submit" value="approved" name="approve_btn">Approve</button>
      <button type="submit" value="rejected" name="reject_btn">Reject</button>
   </form>

   <?php
     getSpecInst($conn, $fid);
      if (isset($_POST["approve_btn"])){
          if ($testAccount == "Bookkeeper"){
            echo "<script>console.log('Approved button pressed.');</script>";
            bookieApproves($conn, $fid);
            sendToBursar($conn, $fid);

            // redirects browser
            header("Location: formReview.php");
            exit();
          } else if ($testAccount == "Bursar"){
            bursarApproves($conn, $fid);
            sendEmailToReimbPerson($conn, $formInfo["reimb_id"], $testUser, $fid);
            
            header("Location: formReview.php");
            exit();
              
          }
      } elseif (isset($_POST["reject_btn"])) {
           if ($testAccount == "Bookkeeper"){
            echo "<script>console.log('Reject button pressed.');</script>";
            bookieRejects($conn, $fid);
      
            // redirect browser
            header("Location: formReview.php");
            exit();
               
          } else if ($testAccount == "Bursar"){
            echo "<script>console.log('Reject button pressed.');</script>";
            bursarRejects($conn, $fid);
            header("Location: formReview.php");
            exit();
              
          }
      }

   ?>
  
    
</div>
</body>


</html>

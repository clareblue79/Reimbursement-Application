<!-- CS 304 Final Project: Wellesley Reimbursement Website
Team name: CHiLY 
Programmers: Clare Lee, Hanae Yaskawa  
File: formTemplate.php                                                                                       
Description: The template for the reimbursement form that treasurers fill out.
Children are newForm.php that has a blank form, and editForm.php that displays
the form with its previously saved values.
-->

<?php
    
   ob_start();
   session_start();
   require_once("header.php");
   
   // gets connection to database and access to db functions
   require_once("setup.php"); 
   
   require_once("redirect.php");
   redirect("Treasurer");
   

?>

<!doctype html>
<html lang="en">

<head>
    <!-- Our JavaScript -->
    <script src="form.js"></script>
    
</head>
    

    
<body id="sofc_form">

<div id="whole_page">

   <div class='header'>
   <h1>SOFC Check Request Form</h1>
   <p>Please fill out the form to request reimbursement. Click save to save your work, or click submit to submit the form and send to the bookkeeper for approval.</p>
   </div>

   <form id="request_form" action= "<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data" ><br>
   
      <label for="date_prepared">Date Prepared: </label>
      <span type="date"><?php echo date('Y-m-d'); ?></span>
      <input type="hidden" name="date_prepared" value="<?php echo date('Y-m-d'); ?>"></input><br>

      <label for="orgs">Organization: </label>
      <select id="orgs" name="orgs" required>
         <!-- children fill in dropdown menu values of organization names -->
         <?php echo $orgOptions; ?>
      </select><br>
      
      <script>
      // Had trouble including in form.js as a function
      // dynamically fills in bookie name and sofc, profit, and clce foapal numbers
      // when an org is selected from drop-down menu
      $("#orgs").change(function(){
         var orgselected = $("#orgs").find("option:selected").val();
         console.log(orgselected + " is the org selected.");
         $.post("fillInOrg.php", {orgid: orgselected}, function(response){
            console.log("The response from fillInOrg.php was "+response);
            $("#bookie").text(jQuery.parseJSON(response)['bookie']);
            $("#sofc_foapal").text(jQuery.parseJSON(response)['sofc']);
            $("#profit_foapal").text(jQuery.parseJSON(response)['profits']);
            $("#clce_foapal").text(jQuery.parseJSON(response)['clce']);
         });
      });
      </script>
      
      
      <label for="bookie">Bookkeeper: </label><span id="bookie" value="<?php echo $bookieVal; ?>"></span><br>

      <p class="instruction">Payments to students, employees, or alumnae require a Banner ID. Payments to vendors require W9 tax information on file in the Controller's Office (Vendor ID).</p>
    
      <label>Student, College Employee, or Vendor requesting reimbursement:</label><br>
      <label for="reimb_bnum">Banner/Vendor ID: </label><input type="text" name="reimb_bnum" id="reimb_bnum" value="<?php echo $reimb_bnumVal; ?>" placeholder="B Number" required></input><br>
      <span class="instruction" type="text" id="bnum_error"></span>
      <input id="bnum_error_exists" name="bnum_error_exists" value="" hidden></input><br>
      
      <script>
      // Had trouble including in form.js as a function
      // Function called to dynamically show error message if the inputted bnumber
      // is not a valid bnumber in the database
      $("#reimb_bnum").on('blur', function(){
         console.log("Called to check reimb bnumber...");
         var reimb_bnum = document.getElementById("reimb_bnum").value;
        
         // calls on php helper file that searches for bnumber in database
         // returns dictionary that is row of result search containing
         // count, fullname and address
         $.post("checkReimbBnum.php", {bnum: reimb_bnum}, function(response) {
            console.log("The response from checkReimbBnum.php was "+response);
            var count = jQuery.parseJSON(response)['count'];
            // if no bnumber is found in database, sets error message 
            // and sets error message exists to be true
            if (count!=1) {
               $("#bnum_error").text("Please enter a valid B Number");
               $("#bnum_error_exists").val("true");
               // sets reimbursement person's name and address to be null
               $("#reimb_name").text("");
               $("#reimb_address").text("");
            }
            // if a bnumber is found in database, sets error message to be null
            // and sets error message exists to be false
            else if(count==1) {
               $("#bnum_error").text("");
               $("#bnum_error_exists").val("false");
               // sets reimbursement person's name and address
               var name = jQuery.parseJSON(response)['fullname'];
               var address = jQuery.parseJSON(response)['address'];
               console.log("name: "+name);
               console.log("address: "+address);
               $("#reimb_name").text(name);
               $("#reimb_address").text(address);
            }
         });
      });
      </script>
      
      <label for="reimb_name">Name: </label>
      <span name="reimb_name" id="reimb_name" value="<?php echo $reimb_nameVal; ?>"></span><br>
      
      <label for="reimb_address">Address: </label>
      <span name="reimb_address" id="reimb_address" value="<?php echo $reimb_addressVal; ?>"></span><br>

    
      <p class="instruction">*If students are being paid for services, they MUST be on college payroll. **ANY payments for services and rentals require vendor tax information to be on file. If the vendor has moved or has not been paid by Wellesley College, a W9 or New Vendor Form (completed by the vendor) must be subitted along with the check request. If the vendor's tax information is not on file, this check request will be returned to you.</p><br>
      
   <label for="purpose">Purpose of this Reimbursement (remember to attach DOCUMENTATION and ORIGINAL RECEIPTS): </label><br>
   <textarea rows="4" cols="100" name="purpose" id="purpose" placeholder="Purpose" value="<?php echo $purposeVal; ?>"></textarea><br><br>

   <p class="instruction">Please fill all rows from the top and delete any empty rows before submitting.</p>
   <table id="events">
      <tr>
         <td>Event</td>
         <td>Date of Event</td>
         <td># of Student Attendees</td>
         <td>Category</td>
         <td>Amount</td>
         <td>Funding Source</td>
      </tr>
      <?php echo $eventsVal; ?>
	   
	<!-- added reimbursement list items go here -->
     
   </table>

 
   <input type="button" id="addEvent" value="+"></input>
   <input type="button" id="removeEvent" value="-"></input>
   <input type="hidden" id="numEvents" name="numEvents" value="<?php echo $numEventsVal; ?>"></input><br>
   
   <script>
   // originally had addEvent() and removeEvent() functions in form.js
   // however could not access those functions from form.js (though calcTotal()
   // addReceipt(), removeReceipt(), addAttendees() and removeAttendees() all worked)
   
   // dynamically adds an event to events table when addEvent button is clicked
   $("#addEvent").on("click", function() {
      // gets current number of reimbursement items and adds one
      var n = parseInt(document.getElementById("numEvents").value)+1;
      console.log("There are now "+ n + " events in hidden value");
      var eventName = '"event'+n +'"';
      var newevent = '<td><input type="text" name=' + eventName + ' placeholder="Event name" required></td>';
      var dateName = '"edate'+n + '"';
      var newdate = '<td><input type="date" name='+ dateName + ' required></td>'; 
      var numName = '"num_attendees'+n + '"';
      var newnum = '<td><input type="number" name='+ numName + ' min="1" step="1" placeholder="Number of attendees" required></td>';
      var categoryName = '"category' + n + '"';
      var newcategory = '<td><input type="text" name=' + categoryName + ' placeholder="Category" required></td>';  
      var amntName = '"amnt'+n + '"';
      var newamnt = '<td><input type="number" name=' + amntName + ' step="0.01" min="0" placeholder="Amount" required></td>';
      var fundsrcName = '"fundsrc'+n+'"';
      var newfundsrc = '<td><select name='+fundsrcName+' required><option value="" disabled selected>Select Source</option><option value="Profits">Profits</option><option value="sofc">SOFC deadline</option><option value="GP">GP org</option><option value="CLCE">CLCE</option></td>';
      var newrow = '<tr id="row'+n+'">'+newevent+newdate+newnum+newcategory+newamnt+newfundsrc+'</tr>';   
      $("#events").append(newrow);
      // updates number of reimbursement items in table
      $("#numEvents").val(n);
      console.log("finished adding reimb item #"+n);   
    });
    
    
    // dynamically removes an event from events table when removeEvent button is clicked
    // only removes an event if there is more than one event row
    $("#removeEvent").on("click", function() {
        // first gets current number of reimbursement items
       var n = parseInt(document.getElementById("numEvents").value);
       // if there is more than one reimbursement item
       if (n>1){
	      $("#events tr:last").remove();
	      // updates number of reimbursement items
	      $("#numEvents").val(n-1);
	      console.log("Called function removeEvent, now " + (document.getElementById('numEvents').value)+" remain");
       }
    });
   </script>
         
         
   <h3>ACCOUNTING INFORMATION (FOAPAL #)</h3>
   <p class="instruction"># in parenthesis indicate the number of digits in the FOAPAL segment.</p>

   <table id="foapals">
      <tr>
         <td>Fund (5)</td>
         <td>Org (4)</td>
         <td>Acct (4)</td>
         <td>Prgm (3)</td>
         <td>Activity (6)</td>
         <td>Location (3)</td>
         <td>Amount*</td>
      </tr>
      <tr>
         <td>81<span id="sofc_foapal"><?php echo $sofc_foapalVal ?></span></td>
         <td>4610</td>
	     <td>7998</td>
	     <td>981</td>
	     <td>SOFC</td>
	     <td><input type="text" name="sofc_loc" value="<?php echo $sofc_locVal; ?>" placeholder="Event location"></input></td>
	     <td>$<input type="number" name="sofc_amnt" id="sofc_amnt" value="<?php echo $sofc_amntVal; ?>" onblur="calcTotal()" step="0.01" min="0" placeholder="Amount"></input></td>
      </tr>
      <tr>
         <td>83<span id="profit_foapal"><?php echo $profit_foapalVal; ?></span></td>
         <td>4620</td>
         <td>7998</td>
         <td>982</td>
	    <td>PROFITS</td>
	    <td><input type="text" name="profit_loc" value="<?php echo $profit_locVal; ?>" placeholder="Event location"></input></td>
	    <td>$<input type="number" name="profit_amnt" id="profit_amnt" value="<?php echo $profit_amntVal; ?>" onblur="calcTotal()" step="0.01" min="0" placeholder="Amount"></input></td>
      </tr>
      <tr>
         <td>83<span id="clce_foapal"><?php echo $clce_foapalVal; ?></span></td>
         <td>4620</td>
         <td>7999</td>
         <td>982</td>
         <td>CLCE</td>
         <td><input type="text" name="clce_loc" value="<?php echo $clce_locVal; ?>" placeholder="Event location"></input></td>
	     <td>$<input type="number" name="clce_amnt" id="clce_amnt" value="<?php echo $clce_amntVal; ?>" onblur="calcTotal()" step="0.01" min="0" placeholder="Amount"></input></td>
      </tr>
      <tr>
         <td></td>
         <td></td>
         <td></td>
         <td></td>
         <td></td>
         <td>TOTAL AMOUNT</td>
         <td>$<span name="ttl_amnt" id="ttl_amnt" value="<?php echo $ttl_amntVal; ?>"></span></td>
      </tr>
   </table>


   
   <h3>Upload Receipts</h3>
   <span class="instruction">Please upload only jpg files.</span><br>
   <div id="receipts">
      <?php echo $receiptsVal; ?>
   </div><br>
      
   <input type="button" onclick="addReceipt()" value="+"></input>
   <input type="button" onclick="removeReceipt()" value="-"></input>
   <input type="hidden" id="numReceipts" name="numReceipts" value="<?php echo $numReceiptsVal; ?>"></input><br>
         

   <h3>Upload Lists of Attendees</h3>
   <span class="instruction">Please upload only jpg files.</span><br>
   <div id="listattendees">
      <?php echo $attendeesVal; ?>
   </div><br>

   <input type="button" onclick="addAttendees()" id="addattendees_btn" value="+"></input>
   <input type="button" onclick="removeAttendees()" id="removeattendees_btn" value="-"></input>
   <input type="hidden" id="numAttendees" name="numAttendees" value="<?php echo $numAttendeesVal; ?>"></input><br>
    

   <h3>SPECIAL INSTRUCTIONS: If you do NOT have direct deposit, please SELECT one of the following:</h3>
      <input type="radio" name="spec_inst" id="send_check" value="send_check" <?php echo $spec_instSend; ?></input>Check sent to address listed above<br>
      <input type="radio" name="spec_inst" id="email_check" value="email_check" <?php echo $spec_instEmail; ?>></input>Person to e-mail when check is ready for pick up at the Cashier's Window in Green Hall: <input type="email" name="email" id="email" value="<?php echo $spec_instEmailVal; ?>" placeholder="email"></input><br><br>
      
      <script>
      // if special instruction to send check is selected,
      // removes required attribute to email input
      $("#send_check").on("click", function(){
         $("#email").removeAttr("required");
      });
      
      // if special instruction to email check is selected,
      // adds required attribute to email input
      $("#email_check").on("click", function(){
         $("#email").attr("required", true);
      });
      
      </script>

   <button type="submit" value="Submit" name="submit_btn">Submit</button>

</form>
        
         
</div>

</body>
</html>         
         
         
         
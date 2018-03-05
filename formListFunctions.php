<!-- CS 304: Final Project
File Name: formListFunctions.php
Programmers: Clare Lee, Hanae Yaskawa
Team Name: CHILY
Last Modified Date: 05/10/2017 

Helper functions to list forms for the bookies, treasurers, and tracker in
forPage.php, formReview.php, reimb.php-->

<?php 

/*  getFormCount functions checks how many forms are in the query based on 
    table and id of the user 
    @param: $conn (db_connection), $ID (uid or bookieID), $tableName (string table name)
    @return: $count (int)
*/
    function getFormCount($conn, $ID, $tableName){
        $count = 0;
            
        if ($tableName == 'forms'){
            $sql = "SELECT count(*) from forms where uid = ? ";
            $resultset = prepared_query($conn, $sql, array($ID));
            $row = $resultset -> fetchRow(MDB2_FETCHMODE_ASSOC);
            $count = $row['count(*)'];
        } 
        
        if ($tableName == 'saved'){
            $sql = "SELECT count(*) from forms where uid = ? AND status='saved'";
            $resultset = prepared_query($conn, $sql, array($ID));
            $row = $resultset -> fetchRow(MDB2_FETCHMODE_ASSOC);
            $count = $row['count(*)'];
        } 
        
        if ($tableName == 'Bookkeeper'){
            $sql = "SELECT count(*) from bookie_approval where (bookieid =? AND status ='not_checked')";
            $resultset = prepared_query($conn, $sql, array($ID)); 
            $row = $resultset -> fetchRow(MDB2_FETCHMODE_ASSOC);
            $count = $row['count(*)'];
        }
        
        if($tableName == 'tracker'){
            $sql = "SELECT count(*) from forms where (reimb_id =? AND status ='submitted')";
            $resultset = prepared_query($conn, $sql, array($ID)); 
            $row = $resultset -> fetchRow(MDB2_FETCHMODE_ASSOC);
            $count = $row['count(*)'];
        }
        
         if($tableName == 'Bursar'){
            $sql = "SELECT count(*) from bursar_approval WHERE status = 'not_checked'";
            $resultset = prepared_query($conn, $sql, array($ID)); 
            $row = $resultset -> fetchRow(MDB2_FETCHMODE_ASSOC);
            $count = $row['count(*)'];
        }
        
        return $count;
    }//end getFormCount()


/*  getFormsForReview echoes all the form information submitted for review under the $bookieID 
    @param: $conn (db_connection), $uid, $accountType
    @return: echoes rows
*/
    function getFormsForReview($conn, $uid, $accountType){
        if ($accountType == 'Bookkeeper'){ 
            $sql = "SELECT fid FROM bookie_approval WHERE (bookieid =? AND status ='not_checked')";  
            $resultset = prepared_query($conn, $sql, array($uid));
        } else if ($accountType == 'Bursar'){
            $sql = "SELECT fid FROM bursar_approval WHERE status='not_checked'";  
            $resultset = prepared_query($conn, $sql, array());
        }
        
        while($row = $resultset -> fetchRow(MDB2_FETCHMODE_ASSOC)){
            $FID = $row['fid'];
            $sql = "SELECT * FROM forms WHERE fid=?";
            $resultset2 = prepared_query($conn, $sql, array($FID));
            $row2 = $resultset2 -> fetchRow(MDB2_FETCHMODE_ASSOC);
            
            $orgname = getOrgName($conn, $row2['orgid']);            
            $ttl = $row2['ttl_amnt'];
            $bnum = getBnumber($conn, $row2['reimb_id']);
            $date_prepared = $row2['date_prepared'];
            
            echo "<tr>";
            echo "<td>$FID</td>";
            echo "<td>$orgname</td>";
            echo "<td>$bnum</td>";
            echo "<td>$ttl</td>";
            echo "<td>$date_prepared</td>";
            echo "<td><a href=showForm.php?fid=$FID>Review</a></td>";
            echo "</tr>";
            
        }  
    }//end getFormsForReview()

/*  getSubmittedForms echoes all the forms submitted by the treasurer
    @param: $conn (db_connection), $ID (current treasuer's uid)
    @return: echoes rows
*/
    function getSubmittedForms($conn, $ID){
        $sql = "SELECT * FROM forms WHERE uid=?";
        $resultset = prepared_query($conn, $sql, array($ID));
        while($row = $resultset -> fetchRow(MDB2_FETCHMODE_ASSOC)){
            $fid = $row['fid'];
            $reimbID = $row['reimb_id'];
            $eventnm = getEventsNames($conn, $fid);  
            $uname = getUsername($conn, $reimbID );
            $bnum = getBnumber($conn, $reimbID);
            $ttl = $row['ttl_amnt'];
            $date_prepared = $row['date_prepared'];
            $status = getStatus($conn, $fid);
            
            //split status string by ':'
            $statusStrings = explode(":", $status);
            
            //echo "<script>console.log($fid);</script>";

            
            echo "<tr>";
            echo "<td>$bnum</td>";
            echo "<td>$eventnm</td>";
            echo "<td>$ttl</td>";
            echo "<td>$date_prepared</td>";
            echo "<td>$status</td>";
            
            if ($statusStrings[0] == "Bookkeeper"){
                //get comments from the bookie_approval table
                getComments($conn, $fid, "bookie");
                
            }
            if ($statusStrings[0] == "Student Bursar"){
                //get comments from the bursar_approval table
                getComments($conn, $fid, "bursar");
            } 
            echo "</tr>";
        }
    }//end getSubmittedForms()


/*  getSavedForms echoes all the forms saved by the treasurer and links it to a edit page
    @param: $conn (db_connection), $ID (current treasuer's uid)
    @return: echoes rows
*/
    function getSavedForms($conn, $ID){
        $sql = "SELECT * FROM forms WHERE uid=?";
        $resultset = prepared_query($conn, $sql, array($ID));
        while($row = $resultset -> fetchRow(MDB2_FETCHMODE_ASSOC)){
            $fid = $row['fid'];
            $reimbID = $row['reimb_id '];
            $eventnm = getEventsNames($conn, $fid);  
            $uname = getUsername($conn, $reimbID);
            $bnum = getBnumber($conn, $reimbID);
            $ttl = $row['ttl_amnt'];
            $date_prepared = $row['date_prepared'];
            
            echo "<tr>";
            echo "<td>$uname</td>";
            echo "<td>$bnum</td>";
            echo "<td>$eventnm</td>";
            echo "<td>$ttl</td>";
            echo "<td>$date_prepared</td>";
            echo "<td><a href='editForm.php?fid=$fid>Edit</a></td>";
            echo "</tr>";
        }
    }//end getSavedForms()


/*  getTrackerForms echoes all the forms submitted under the current user's uid
    @param: $conn (db_connection), $ID (current treasuer's uid)
    @return: echoes rows
*/
    function getTrackerForms($conn, $ID){
        $sql = "SELECT fid, orgid, date_prepared, ttl_amnt FROM forms WHERE (reimb_id = ? AND status ='submitted')";
        $resultset = prepared_query($conn, $sql, array($ID));
        while($row = $resultset -> fetchRow(MDB2_FETCHMODE_ASSOC)){ 
            $fid = $row['fid'];
            $orgName = getOrgName($conn, $row['orgid']);
            $eventnm = getEventsNames($conn, $row['fid']);
            $ttl = $row['ttl_amnt'];
            $date_prepared = $row['date_prepared'];
            $status = getStatus($conn, $fid);

            echo "<tr>";
            echo "<td>$orgName</td>";
            echo "<td>$eventnm</td>";
            echo "<td>$ttl</td>";
            echo "<td>$date_prepared</td>";
            echo "<td>$status</td>";
            echo "</tr>";
        }
    }//end getTrackerForms()

/*  getStatus queries the status of the form by fid and searches the status of the 
form in both bookie approval and bursar approval table
    @param: $conn (db_connection), $fid
    @return: $status (String status of the current form)
*/
   function getStatus($conn, $fid){
        $sql = "SELECT status FROM bookie_approval WHERE fid =?";
        $resultset = prepared_query($conn, $sql, array($fid));
        $row = $resultset -> fetchRow(MDB2_FETCHMODE_ASSOC);
        $bookieStatus = $row['status'];
       // echo "<script>console.log('boookie status: $bookieStatus');</script>";
       if ($bookieStatus == 'approved'){
           //if it has been already approved by the bookie, check bursar status
            $sql2 = "SELECT status FROM bursar_approval WHERE fid =?";
            $resultset2 = prepared_query($conn, $sql2, array($fid));
            $row2 = $resultset2 -> fetchRow(MDB2_FETCHMODE_ASSOC);
           
            return "Student Bursar: ".$row2['status']. "<br> Bookkeeper: ". $row['status'];
       } else {
           return "Bookkeeper: ". $bookieStatus;
           
       }
    }//end getStatus()

/*  getOrgID queries the orgid of the form fid
    @param: $conn (db_connection), $fid
    @return: $orgid
*/
    function getOrgid($conn, $fid) {
        $fsql = "SELECT orgid FROM forms WHERE fid=$fid";
        $fresult = query($conn, $fsql);
        $row = $fresult->fetchRow(MDB2_FETCHMODE_ASSOC);
        $orgid = $row['orgid']; 
        echo ("<script>console.log( 'Successfully got org $orgid' );</script>");
        return $orgid;
    }//end getOrgid()

/*  getOrgName queries the name of the org by orgID
    @param: $conn (db_connection), $orgID
    @return: $orgname (String orgname of the org)
*/
    function getOrgName($conn, $orgID){
        $sql = "SELECT orgname FROM orgs WHERE orgid =?";
        $resultset = prepared_query($conn, $sql, array($orgID));
        $row = $resultset -> fetchRow(MDB2_FETCHMODE_ASSOC);
        
        return $row['orgname'];       
    }//end getOrgName()

/*  getReimbID queries the uid of the reimbursement person by fid
    @param: $conn (db_connection), $fid
    @return: $reimb_id
*/
    function getReimbID($conn, $fid) {
        $fsql = "SELECT reimb_id FROM forms WHERE fid=$fid";
        $fresult = query($conn, $fsql);
        $row = $fresult->fetchRow(MDB2_FETCHMODE_ASSOC);
        return $row['reimb_id'];
    }//end getReimbId()

/*  getUsername queries the name of the user by uid from accts
    @param: $conn (db_connection), $uid
    @return: $username (String username)
*/
    function getUsername($conn, $uid){
        $sql = "SELECT username FROM accts WHERE uid =?";
        $resultset = prepared_query($conn, $sql, array($uid));
        $row = $resultset -> fetchRow(MDB2_FETCHMODE_ASSOC);
        
        return $row['username'];       
    }//end getUsername()

/*  getFullname queries the name of the user by uid from accts
    @param: $conn (db_connection), $uid
    @return: $fullname (String fullname)
*/
    function getFullname($conn, $uid){
        $sql = "SELECT fullname FROM accts WHERE uid =?";
        $resultset = prepared_query($conn, $sql, array($uid));
        $row = $resultset -> fetchRow(MDB2_FETCHMODE_ASSOC);
        
        return $row['fullname'];       
    }//end getFullname()

/*  getBnumber queries the bnumber of the user by uid from accts
    @param: $conn (db_connection), $uid
    @return: $bnumber (String bnumber)
*/
    function getBnumber($conn, $uid){
        $sql = "SELECT bnumber FROM accts WHERE uid =?";
        $resultset = prepared_query($conn, $sql, array($uid));
        $row = $resultset -> fetchRow(MDB2_FETCHMODE_ASSOC);
        
        return $row['bnumber'];       
    }//end getBnumber()

/*  getAddress queries the address of the user by uid from accts
    @param: $conn (db_connection), $uid
    @return: $bnumber (String bnumber)
*/
    function getAddress($conn, $uid){
        $sql = "SELECT address FROM accts WHERE uid =?";
        $resultset = prepared_query($conn, $sql, array($uid));
        $row = $resultset -> fetchRow(MDB2_FETCHMODE_ASSOC);
        
        return $row['address'];       
    }//end getAddress()


/*  getEventsNames queries all the events name submitted under same fid
    and returns a single string with all the events name appended to 
    it. 
    @param: $conn (db_connection), $fid
    @return: $events (String events)
*/
    function getEventsNames($conn, $fid){
        $sql = "SELECT ename FROM events WHERE fid =?";
        $resultset = prepared_query($conn, $sql, array($fid));
        $eventlist = "";
        while($row = $resultset -> fetchRow(MDB2_FETCHMODE_ASSOC)){
            $eventlist .= $row['ename']." ";
        }
        return $eventlist;
    }//end getEventsNames()


/*  queries the id of the bookie who is in charge of the form with fid
    @param: $conn (db_connection), $fid
    @return: $bookieid
*/
    function getBookieInCharge($conn, $fid){
        $sql = "SELECT orgid FROM forms WHERE fid = ?";
        $resultset = prepared_query($conn, $sql, array($fid));
        $row = $resultset -> fetchRow(MDB2_FETCHMODE_ASSOC);
        
        $orgid = $row['orgid'];
        
        $sql2 = "SELECT bookieid FROM orgs WHERE orgid = ?";
        $resultset2 = prepared_query($conn, $sql2, array($orgid));
        $row2 = $resultset2 -> fetchRow(MDB2_FETCHMODE_ASSOC);
        
        return $bookieid = $row2['bookieid'];
        
    }

/*  queries comments attached to the form when rejected in both 
    bookie_approval table and from bursar_approval table based on input
    @param: $conn (db_connection), $fid, $table
    @return: echos $comment modal and the button
*/
    function getComments($conn, $fid, $table){
        if ($table == "bookie"){
            $sql = "SELECT comment, status FROM bookie_approval WHERE fid = ?";
            $resultset = prepared_query($conn, $sql, array($fid));
            $row = $resultset -> fetchRow(MDB2_FETCHMODE_ASSOC);
            $comments = $row["comment"];
            $status = $row['status'];
            if ($status == 'not_checked'){
                echo "<td>None</td>";
            } else{
            echo "<!-- Trigger the modal with a button -->
                <td><button type='button' class='btn btn-info btn-lg' data-toggle='modal' data-target='#myModal$fid'>CLICK</button></td>";
             
            
            
            echo "<!-- Modal -->
                <div id='myModal$fid' class='modal fade' role='dialog'>
                <div class='modal-dialog'>

                <!-- Modal content-->
                <div class='modal-content'>
                <div class='modal-header'>
                <h4 class='modal-title'>Comments From Your Bookkeeper</h4>
                </div>
                <div class='modal-body'>
                <p>$comments</p>
                </div>
                <div class='modal-footer'>
                <button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
                </div>
                </div>

                </div>
                </div>";
            }
        }
        
        if ($table == "bursar"){
            $sql = "SELECT comment, status FROM bursar_approval WHERE fid = ?";
            $resultset = prepared_query($conn, $sql, array($fid));
            $row = $resultset -> fetchRow(MDB2_FETCHMODE_ASSOC);
            $comments = $row['comment'];
            $status = $row['status'];
            if ($status == 'not_checked'){
                echo "<td>None</td>";
            } else{
            echo "<!-- Trigger the modal with a button -->
                <td><button type='button' class='btn btn-info btn-lg' data-toggle='modal' data-target='#myModal$fid'>CLICK</button></td>";
          
                
            echo "<!-- Modal -->
                <div id='myModal$fid' class='modal fade' role='dialog'>
                <div class='modal-dialog'>

                <!-- Modal content-->
                <div class='modal-content'>
                <div class='modal-header'>
                <h4 class='modal-title'>Comments From The Student Bursar</h4>
                </div>
                <div class='modal-body'>
                <p>$comments</p>
                </div>
                <div class='modal-footer'>
                <button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
                </div>
                </div>
                </div>
                </div>";
            }
        }       
    }
//ENDFILE

?>
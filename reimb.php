<!-- CS 304: Final Project
File Name: reimb.php
Programmers: Clare Lee, Hanae Yaskawa
Team Name: CHILY
Last Modified Date: 05/10/2017 

ALL USERS have access to this page. Students can view
the status of their reimbursement submitted under their names.

CURRENTLY DUMMY 
Reimbursment tracker for our Reimbursement Application -->

<html lang='en'>
    <?php
        ob_start();
        session_start();
        require_once("/home/cs304/public_html/php/DB-functions.php");
        require_once('navBar.php');
        require_once('setup.php');
        require_once('redirect.php');
        require_once('header.php');
        require_once('formListFunctions.php');
        redirect("generic");    
    ?>
   
<body>
    <?php
        getNav("reimb",$_SESSION['accountType']);
        $userID = $_SESSION['user'];
    
        //work on this
        $arrlength = getFormCount($conn, $userID , 'tracker');
    ?>
   
    
    <div class="profile">
    

       <h1> Reimbursement Tracker</h1>
        
        <?php echo "<p> There are $arrlength reimbursement forms submitted under your username. </p>"; ?>
        <div class="about">
        
            <table id="savedForms">
            <tr>
                <td width=12%>Org Name</td>
	            <td width=33%>Events</td>
	            <td>Total Amount($)</td>
                <td>Submitted Date</td>
	            <td width=20%>Status</td>
            </tr>
                
        <?php
            if($arrlength == 0){
                echo "<tr><td colspan=6> Please contact your treasurer if you need reimbursement.</td><tr>";
            } else {
                getTrackerForms($conn, $userID);
            }
        ?>
            </table>
        </div>   
        </div>
        
</body>

</html>
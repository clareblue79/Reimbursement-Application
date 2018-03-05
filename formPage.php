<!-- CS 304: Final Project
File Name: formPage.php
Programmers: Clare Lee, Hanae Yaskawa
Team Name: CHILY
Last Modified Date: 05/10/2017 

ONLY TREASURERS have access to this page.
Treasuers can submit a new for or check their submissions here-->

<html lang='en'>
<?php
    ob_start();
    session_start();
    require_once("/home/cs304/public_html/php/DB-functions.php");
    require_once('navBar.php');
    require_once('setup.php');        
    require_once('redirect.php');
    require_once('formListFunctions.php');
    require_once('header.php');
    
    redirect("Treasurer");
?>
    
<body>
    <?php

        getNav("formPage",$_SESSION['accountType']);
        $userID = $_SESSION['user'];
        $arrlength = getFormCount($conn, $userID , 'forms');
    
    ?>
   
    <div class="profile">
    
       <h1> Forms Submitted </h1>
         <?php echo "<p> You have $arrlength submitted forms. </p>"; ?>
        
        <div class="about">
        <table id="savedForms">
            
        <tr>
           <td width=10%>BNumber</td>
	       <td width =30%>Events</td>
	       <td width = 10%>Total Amount($)</td>
	       <td>Submitted Date </td>
	       <td width=25%>Status</td>
           <td> Comments</td>
        </tr>
        
        <?php
            if($arrlength == 0){
                echo "<tr><td colspan=7> Click <a href='newForm.php'>here</a> to submit a new form.</td><tr>";
            } else {
                 getSubmittedForms($conn, $userID);
            }
        ?>
            

	
   </table>
            
        
        </div>
        
     </div>
        

</body>

</html>

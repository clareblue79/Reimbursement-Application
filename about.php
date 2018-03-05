<!-- CS 304: Final Project
File Name:about.php
Programmers: Clare Lee, Hanae Yaskawa
Team Name: CHILY
Last Modified Date: 05/10/2017 

ALL USERS have access to this page, once logged in.
About Page for our Reimbursement Application -->

<html lang='en'>
    <?php //all the requires
    
        ob_start();
        session_start();
        require_once("/home/cs304/public_html/php/DB-functions.php");
        require_once('chily-dsn.inc');
        require_once('navBar.php');
        require_once('setup.php');
        require_once('redirect.php');
        //setting redirects based on access level 
        redirect("generic");    
        require_once("header.php");
    ?>
<body>
    <?php
        getNav("about",$_SESSION['accountType']);
    ?>
   
    
    <div class="profile">
    

       <h1> About the Wellesley Reimbursement Application</h1>
        
        <div class="about">
        
        <h4> About Page for our Wellesely Reimbursement App</h4>
        
        <p> Some description....Lorem ipsum dolor sit amet, consectetur adipisicing elit,
      sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad
      minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea
      commodo consequat.</p>
            
             <p> Some description....Lorem ipsum dolor sit amet, consectetur adipisicing elit,
      sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad
      minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea
      commodo consequat.</p>
            
             <p> Some description....Lorem ipsum dolor sit amet, consectetur adipisicing elit,
      sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad
      minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea
      commodo consequat.</p>
            
        
        </div>
        
     </div>
        

</body>

</html>

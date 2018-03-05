<!-- CS 304: Final Project
File Name: accountHome.php
Programmers: Clare Lee, Hanae Yaskawa
Team Name: CHILY
Last Modified Date: 05/10/2017 

ALL USERS will have access to this page once logged in, 
but the page will display different things based on the user's
account type (the nav bar is dynamically displayed)

Account Home Page for our Reimbursement Application -->

<html lang='en'>
     <?php //all the requires
        ob_start();
        session_start();
        require_once("/home/cs304/public_html/php/DB-functions.php");
        require_once('navBar.php');
        require_once('setup.php');
        require_once('redirect.php');
        require_once("header.php");
        redirect("generic");
    ?>
    
<body>
    <?php
        $accountType = $_SESSION['accountType'];
        // select loggedin users detail
        $sql = "SELECT * FROM accts WHERE uid = ?";
        $resultset = prepared_query($conn, $sql, array($_SESSION['user']));
        $row = $resultset -> fetchRow(MDB2_FETCHMODE_ASSOC);
        $name = $row["fullname"];
        echo "<script>console.log( 'Welcome to your profile $name ');</script>";
       
        getNav("home", $accountType);
    ?>
    
    <div class="profile">
      <?php 
        $account = ucfirst($accountType);
        echo "<h1> Wellesley Reimbursement - $account Page</h1>";
        echo "<p class='welcome'>Welcome back $name!</p>";
      ?>
        
        <div class="about">
        
        <h4> About the Wellesely Reimbursement Application</h4>
        <br>
        <p>Ever wondered about when you are getting reimbursed? Ever wondered what happened to your 
            reimbursement forms? Ever had trouble organizing all the submissions? We present you the
            Wellesley Reimbursement Application as our solution. Our application faciliates the student reimbursement
            request process by digitizing the form submission process, organization, and tracking allowing
            everyone involved in the process stay informed and organized. </p>
            
        <p> Wellesley’s current system is paper-based, slow, and not transparent. For students who 
            submit multiple reimbursements, it is very stressful to wait for months without knowing 
            the status of their forms. Moreover, there is no simple way for treasurers, bookkeepers, and 
            the Student Bursar to organize their forms and keep track of the approval process. We wanted 
            to make the reimbursement process simple and fun for everyone! </p>
            
        <p>Thank you so much for using our application and please feel to contact us if you have any 
            questions or concerns.  </p>
            
        <br>
            
        <address>
            Written by <a href="mailto:clee19@wellesley.edu">Clare Frances Lee</a> and 
            <a href="mailto:hyaskawa@wellesley.edu">Hanae Yaskawa</a><br> 
            <br>
            CS 304: Databases with Web Interfaces <br>
            Taught by Professor Scott Anderson<br>
            May, 2017
            </address>
        </div>
        
     </div>
        
</body>

</html>
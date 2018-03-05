<!-- CS 304: Final Project
File Name: account.php
Programmers: Clare Lee, Hanae Yaskawa
Team Name: CHILY
Last Modified Date: 05/10/2017 

ALL USERS have access to this page once logged in.
Account information Page for our Reimbursement Application
Users can update their information on this page
-->

<html lang='en'>

    <?php
    //all the requires
        ob_start();
        session_start();
        require_once("/home/cs304/public_html/php/DB-functions.php");
        require_once('navBar.php');
        require_once('setup.php');
        require_once('redirect.php');
        redirect("generic");
    
        require_once('header.php');
    ?>
    
<body>
    
    <?php
     
        // select loggedin users account information
        $sql = "SELECT * FROM accts WHERE uid = ?";
        $resultset = prepared_query($conn, $sql, array($_SESSION['user']));
        $row = $resultset -> fetchRow(MDB2_FETCHMODE_ASSOC);
        
        getNav("account",$_SESSION['accountType']);
        $bNumError = "";
    
        if (isset($_REQUEST['fullname']) && isset($_REQUEST['address']) && isset($_REQUEST['bNum'])) {
            $fullname = htmlspecialchars($_POST['fullname']);
            $address = htmlspecialchars($_POST['address']);
            $bNum = htmlspecialchars($_POST['bNum']);
        
            if(strlen($bNum) != 9){
                $bNumError = 'BNumber must be 9 characters.';
            }
        
            //if there is a change in input
            if($row['fullname'] != $fullname || $row['address'] != $address|| $row['bnumber'] != $bNum){
                if($bNumError ==""){
                    //only if there is no Bnumerror
                    
                    $sql = "UPDATE accts SET fullname = ?, address = ?, bnumber = ? WHERE uid = ?";
                    $resultset = prepared_query($conn, $sql, array($fullname, $address, $bNum, $_SESSION['user'])); 
                    echo ("<script>console.log('Successful update ')</script>");            
                    header("Location: account.php"); /* Redirect browser */
                    exit();
                }
            }else{
                echo ("<script>console.log('There was no change....')</script>");
            }
        }else{
            echo ("<script>console.log('Initial load...')</script>");
        }
    ?>
   
    
    <div class="profile">
    
       <h1> Your Account Settings </h1>
        
        <div class="about">
            <p class='notimessage'> *Please contact us if you need to update your account type, username, or password*</p>
            <form class='accountSettings' action=<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?> method=post> 
        
                <br><label for="fullname"><b>Full Name</b></label>
                <input type="fullname" name="fullname" value="<?php echo $row['fullname'] ?>" required>
        
                <label for="address"><b>Address</b></label>
                <input type="address" value="<?php echo $row['address'] ?>" name="address" required>
        
                <label><b>BNummber</b></label>
                <input type="bnum" value="<?php echo $row['bnumber'] ?>" name="bNum" required>
                <p class='message'><span><?php echo $bNumError; ?></span></p>
        
                <button type="submit">Save</button>
            </form>
        </div>        
    </div>        
</body>

</html>
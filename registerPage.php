<!-- CS 304: Final Project
File Name: registerPage.php
Team Name: CHILY
Programmers: Clare Lee, Hanae Yaskawa
Last Modified Date: 05/10/2017 

Registration page for new accounts -->

<html lang='en'>
        
 <?php 
    ob_start();
    session_start();
    
    require_once("/home/cs304/public_html/php/DB-functions.php");
    require_once('chily-dsn.inc');
    require_once('accountFunctions.php');
    require_once('setup.php');
    require_once('redirect.php');
    require_once('header.php');
    
    redirect("index"); //so logged in user will never see this page
    $noError = true;
    
    if(isset($_POST['signup'])){  
        $accountType = htmlspecialchars($_POST['accountType']);
        $uname = htmlspecialchars($_POST['uname']);
        $psw = $_POST['psw']; //does not need htmlspecialchars as it will never be displayed
        $confirmpsw = htmlspecialchars($_POST['confirmpsw']);
        $fullname = htmlspecialchars($_POST['fullname']);
        $address = htmlspecialchars($_POST['address']);
        $bNum = htmlspecialchars($_POST['bNum']);
        
        
        if(existUser($conn, $uname)){
            echo "<script> console.log('$uname exists in the database...');</script>";
            $userNameError = "Username already exists.";
            
            $noError = false;
        } else if(!filter_var($uname,FILTER_VALIDATE_EMAIL)){
            //not valid email address
            $userNameError = "Please type in a valid email address.";
            $noError =false;
            
        }else {
            
            $userNameError = '';
        }
        
        if(strlen($psw) < 7){
            $tooShortError = "Password must be longer than 7 characters";
            $noError = false;
        }else {
            $tooShortError = '';
        }
        
        if(strcmp($psw, $confirmpsw)!=0) { //=== is strict comparison 
            $confirmPasswordError = "Please confirm password again.";
            $noError = false;
        } else {
             $confirmPasswordError = '' ;
        }
        
        if(strlen($bNum) != 9){
            $bNumError = 'BNumber must be 9 characters.';
            $noError =false;
        } else {
            $bNumError = '';
        }
        
        
        if($noError){
            $sql = "INSERT INTO accts(username, password, fullname, address, bNumber, acct_type) VALUES(?,?,?,?,?,?)";
            $encrypted = hash('sha256', $psw);
            $resultset = prepared_query($conn, $sql, array($uname, $encrypted, $fullname, $address, $bNum, $accountType)); 
            echo ("<script>console.log('Successful signup ');</script>");            
            header("Location: index.php"); /* Redirect browser */
            exit();
            } else{
            
            echo "<script> console.log('some error....');</script>";
            
            }
        
        } else {
        //when first loaded
        $userNameError = '';
        $tooShortError = '';
        $confirmPasswordError = '' ;
        $bNumError = '';
        
    }


?>
    
    <body>
    <div class='header'>
        <h1> Sign Up and Get Started! </h1>
        <p> Please fill out the following form. All information is required to register.</p>
    </div>
    
    <form class='signup' action=<?php echo $_SERVER['PHP_SELF']; ?> method=post>

    <div class="container"> 
        <label>Account type: </label>
        <select name="accountType">
            <option>Student</option>
            <option>Treasurer</option>
            <option>Bookkeeper</option>
            <option>Employee</option>
            <option>Vendor</option>
            <option>Master</option>
        </select>
        
        <br><label for="email-address"><b>Username</b></label>
        <input type="text" placeholder="Enter Email Address" name="uname" required>
        <p class='message'><span><?php echo $userNameError; ?></span></p>


        <label for="password"><b>Password</b></label>
        <input type="password" placeholder="Enter Password" name="psw" required>
         <p class='message'><span><?php echo $tooShortError; ?></span></p>
        
        
        <label for="password-confirmation"><b>Confirm Password</b></label>
        <input type="password" placeholder="Re-Enter Password" name="confirmpsw" for="password confirmation" required>
        <p class='message'><span><?php echo $confirmPasswordError; ?></span></p>
 
        
        <br><label for="fullname"><b>Full Name</b></label>
        <input type="fullname" placeholder="Enter Full Name" name="fullname" required>
        
        <label for="address"><b>Address</b></label>
        <input type="address" placeholder="Enter Address" name="address" required>
        
        <label for="bnumber"><b>BNumber</b></label>
        <input type="bnum" placeholder="Enter Banner Number" name="bNum" required>
        <p class='message'><span><?php echo $bNumError; ?></span></p>
        
        <button type="submit" name="signup">Sign Up</button>
        <br> <span> <a href="index.php">Already have an account? Sign in here</a></span>
    </div>
        
</form>
    

    
    
</body>
    
</html>
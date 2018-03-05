<!-- CS 304: Final Project
File Name: index.php
Programmers: Clare Lee, Hanae Yaskawa
Team Name: CHILY
Last Modified Date: 05/10/2017 

The login page for our Reimbursement Application -->

<html lang='en'>
    
<?php 
    ob_start();
    session_start();
    require_once("/home/cs304/public_html/php/DB-functions.php");
    require_once('setup.php');
    require_once('redirect.php');
    require_once('accountFunctions.php');
    require_once('header.php');
 
    redirect("index"); //so logged in users will never see this page

    if (isset($_POST['loginbtn'])){
         echo ("<script>console.log('index...')</script>");
        
        $uname = htmlspecialchars($_POST['uname']);
        
        //encrypt password using php hash 
        $psw = hash('sha256', $_POST['psw']); //hmtlspecialchars not needed for psw as it is not displaed
        echo ("<script>console.log('loggin in....')</script>");
        
        if (correctCredentials($conn, $uname, $psw)){
            echo ("<script>console.log('almost there...')</script>");
            header("Location: accountHome.php"); 
            exit();   
            
        } else {
            $userNameError = 'Incorrect login credentials. Please try again!';
            echo ("<script>console.log('login failed')</script>");
        }   
        
    }else{
        //loaded first time
         echo ("<script>console.log('loading first time...')</script>");
        $userNameError = '';
    }
?>
    
<body>
    <div class='header'>
        <h1> Wellesley Reimbursement </h1>
        <p> Welcome to Wellesley Reimbursement </p>
    </div>
    
    <form class='login' action=<?php echo $_SERVER['PHP_SELF'];?> method='post'>

    <div class="container">
        <p class='message'><span><?php echo $userNameError; ?></span></p>
        
      
        <label for="username"><b>Username</b></label>
        <input type="text" placeholder="Enter Username" name="uname" required>

        <label for="password"><b>Password</b></label>
        <input type="password" placeholder="Enter Password" name="psw" required>
        
        <button type="submit" name='loginbtn'>Login</button>
        
        <br> <span> <a href="registerPage.php">Sign up to make an account</a></span>
        
        
    </div>
        
</form>

</body>
    
</html>
<!-- CS 304: Final Project
File Name: redirect.php
Team Name: CHILY
Programmers: Clare Lee, Hanae Yaskawa
Last Modified Date: 05/10/2017 

Php helper function dealing with all the 
redirects based on the account's access -->

    
<?php 
/*  redirection function controls the page access based on the 
    current user's account type. Each page in our application 
    has a differenct access limit, and this function redirects 
    to the appropriate pages based on the account type if the 
    current user does not have the access to the page. 
    
    @param: $access (String of access type)
    i.e. if you call redirect("generic") on a page, all accounts have
    access to that page as long as you are logged in. If you call 
    redirect("treasurer") on a page, only treasurers can see the page, 
    if not the user will be redirected.
    
    @return: redirect to the appropriate page
*/
function redirect($access){
    if (isset($_SESSION['accountType'])){
        $accountType = $_SESSION['accountType'];
    }
    
    //simple redirect to the account home page if user is logged in 
    if($access == "index"){
        if(isset($_SESSION['user']) ) {
            header("Location: accountHome.php");
            exit();
        }
    }
    
    //all account types have access as long as
    //the user is logged in
    if($access == "generic"){
        if(!isset($_SESSION['user']) ) {
            echo "<scrip>console.log('You must log in to access this page')</script>;";
            header("Location: index.php");
            exit();
        }
    }

    
    //only treasurers have access or else redirected 
    if ($access == "treasurer"){
        if(!isset($_SESSION['user']) ) {
            echo "<scrip>console.log('You must log in to access this page')</script>;";
            header("Location: index.php");
            exit();
        }
        if($accountType != "Treasurer"){
            echo "<scrip>console.log('You do not have access to this page as a $accountType...')</script>;";
            header("Location: accountHome.php");
            exit();
        }
    }
    
    //only bookkeepers have access or else redirected
    if($access == "bookkeeper"){
        if(!isset($_SESSION['user']) ) {
            echo "<scrip>console.log('You must log in to access this page')</script>;";
            header("Location: index.php");
            exit();
        }
        if($accountType != "Bookkeeper"){
            echo "<scrip>console.log('You do not have access to this page as a $accountType...')</script>;";
            header("Location: accountHome.php");
            exit();
        }      
    }
    
    //only bookkeepers or student bursar will have access (this is for the form review page
    //and they are the only one who can approve or reject)
    if ($access == "review"){
         if(!isset($_SESSION['user']) ) {
            echo "<scrip>console.log('You must log in to access this page')</script>;";
            header("Location: index.php");
            exit();
        }
        
        if($accountType != "Bookkeeper" && $accountType != "Bursar"){
            header("Location: accountHome.php");
            exit();
        }
    }
    
}//end redirect()


/*  redirectFormReview is a special helper function that redirects the page if the current accessing 
 user is neither a bursar nor the bookkeeper in charge of the org the form is submitted under
 We defined this function because of the form of the showForm
    @param: $uid, $bookieInCharge
    @return: redirect
*/

function redirectFormReview($uid, $bookieInCharge){
    if(!isset($_SESSION['user'])) {
            header("Location: index.php");
            exit();
    }
    //compares if the current uid to bookieincharge id and checks if accounttype is a student bursar
    if($uid != $bookieInCharge && $_SESSION['accountType'] != "Bursar" ){
            header("Location: accountHome.php");
            exit();
    }
}//end redirectFormReview()

//ENDFILE
?>
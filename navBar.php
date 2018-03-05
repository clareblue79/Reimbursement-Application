<!-- CS 304: Final Project
File Name: navBar.php
Team Name: CHILY
Programmers: Clare Lee, Hanae Yaskawa
Last Modified Date: 05/10/2017 

The getNav function in this php file dynamically displays the navbar based on the
account type of the current user as differents accounts have different access to 
different pages. 
-->

    
<?php 

/* getNave function echoes the nav bar element of the page based on the account type
 @param: $activePage (String name of the current active page), 
         $accountType (String name of the current user's account type)
         
 @return: echos the nav bar element appropriately
*/
function getNav($activePage, $accountType){
    //All accounts have the links to the following pages:
    echo "<div class='navbar'> <ul>";
        
    if ($activePage == "home"){
        echo "<li><a class = 'active' href='accountHome.php'>Home</a></li>";
    } else {
        echo "<li><a  href='accountHome.php'>Home</a></li>";
    }
    
    if ($activePage == 'reimb'){
        echo "<li><a class='active' href='reimb.php'>My Reimbursements</a></li>";
    } else {
        echo "<li><a href='reimb.php'>My Reimbursements</a></li>";
    }
    
    //if account type is treasure add link to form submission page
    if ($accountType =="Treasurer"){
        
        if ($activePage == 'formPage'){
            echo "<li><a class='active' href='formPage.php'>Submissions</a></li>";
        }else{
            echo "<li><a href='formPage.php'>Submissions</a></li>";
        }
            echo "<li><a href='newForm.php'> Submit New Form </a></li>";
    } 
    
    //if account type is bookkeeper add link to form review page
    else if ($accountType =="Bookkeeper" || $accountType == "Bursar"){        
        if ($activePage == 'formReview'){
            echo "<li><a class='active' href='formReview.php'> Form Review </a></li>";
        } else{
            echo "<li><a href='formReview.php'> Form Review </a></li>";
        }
    }
    
    //again all accounts have the following links
      if ($activePage == 'account'){
        echo  "<li><a class='active' href='account.php'>My Account</a></li>";
    } else{
        echo  "<li><a href='account.php'>My Account</a></li>";
    }
    
     echo"<li><a id='logout' href='logout.php?logout'><span class='glyphicon glyphicon-log-out'></span>&nbsp;Sign Out</a></li>
        </ul> </div>";
    
}//end getNav()
 
//ENDFILE
?>



<!-- CS 304: Final Project
File Name: signup.php
Team Name: CHILY
Programmers: Clare Lee, Hanae Yaskawa
Last Modified Date: 04/18/2017 

Sign up php functions -->

    
<?php 
/* email validation??
  if ( !filter_var($email,FILTER_VALIDATE_EMAIL) ) {
  }*/
    


function existUser($conn, $username){
    
   // echo "<script> console.log('existUSer called with $username');</script>";
    $sql = "SELECT count(*) FROM accts WHERE username = ?";
    
    $resultset = prepared_query($conn, $sql, array($username));
    $row = $resultset -> fetchRow(MDB2_FETCHMODE_ASSOC);
    
    $count = $row["count(*)"];
    
    
    if ($count > 0){
        echo "<script> console.log('count = $count');</script>";
        return true;
    } else {
        echo "<script> console.log('new user; count = $count');</script>";
        return false;
    }
}

?>



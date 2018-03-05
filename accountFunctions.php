<!-- CS 304: Final Project
File Name: accountFunctions.php
Team Name: CHILY
Programmers: Clare Lee, Hanae Yaskawa
Last Modified Date: 05/10/2017 

Php helper functions that deals with account information 
and account registration -->

    
<?php 

/*  existUser function searches the database to check if the username 
    already exists or not
    @param: $conn (db connection), $username(username string)
    @return: boolean
*/
function existUser($conn, $username){
    $sql = "SELECT count(*) FROM accts WHERE username = ?";
    
    $resultset = prepared_query($conn, $sql, array($username));
    $row = $resultset -> fetchRow(MDB2_FETCHMODE_ASSOC);
    
    $count = $row["count(*)"];
    
    if ($count > 0){
        return true;
    } else {
        return false;
    }
}//end existUser()

/*  correctCredentials function searches the database to check if the
    user's login credentials match the database
    @param: $conn (db connection), $username(username string), $password (string)
    @return: boolean
*/
function correctCredentials($conn, $username, $password){
    
        $sql = "SELECT uid, count(*), username, password, acct_type FROM accts WHERE username = ?";
        $resultset = prepared_query($conn, $sql, array($username)); 
        $row = $resultset -> fetchRow(MDB2_FETCHMODE_ASSOC);
        $count = $row["count(*)"];
        $checkpsw = $row["password"];
        
        if ($count == 1 && $checkpsw === $password){
            echo ("<script>console.log('Successful login')</script>");
            //if correct credentials are given, set session values and redirect to account home page 
            $_SESSION['user'] = $row['uid'];
            $_SESSION['accountType'] = $row['acct_type'];
            return true;
        } else {
             echo ("<script>console.log('Login failed...')</script>");
            return false;
        }
}//end correctCredentials()

?>



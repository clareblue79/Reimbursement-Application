<!-- CS 304: Final Project
File Name: formReview.php
Programmers: Clare Lee, Hanae Yaskawa
Team Name: CHILY
Last Modified Date: 05/10/2017 

ONLY BOOKKEEPERS and STUDENT BURSAR have access to this page.
Bookkeepers can check all the forms submitted 
for review under the orgs they are in charge of.
And studnet bursar can check all the forms submitted for any org. 
The page lists all the forms for review, and links to 
the approval page-->

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
    redirect("review");
?>
    
<body>
    <?php
        $accountType = $_SESSION['accountType'];
        getNav("formReview", $accountType);
        $userID = $_SESSION['user'];
        $arrlength = getFormCount($conn, $userID , $accountType);
    ?>
    
    <div class="profile">
        
       <h1> Forms to Review</h1>
        
        <?php echo "<p> You have $arrlength forms to review. </p>"; ?>
            <div class="about">
            <table id="savedForms">
            <tr>
	            <td>FID </td>
                <td>Org Name</td>
                <td>BNumber</td>
                <td>Total Amount($)</td>
                <td>Sumbitted Date </td>
                <td>Link</td>
            </tr>
                
        <?php
            if($arrlength == 0){
                echo "<tr><td colspan=6>You have reviewed everything!</td><tr>";
            } else {
                getFormsForReview($conn, $userID, $accountType);
            }
        ?>
	
            </table>
            </div>
    </div>
        
</body>

</html>


/* CS 304 Final Project: Wellesley Reimbursement Website
Team name: CHiLY
Programmers: Clare Lee, Hanae Yaskawa
File: form.js
Description: This adds asynchronous functions to the reimbursement form, allowing for extra features to appear dynamically on the form as the treasurer enters data.
*/

// ----------
// Function called to dynamically calculate total amount asked for reimbursement
// Totals amount from SOFC, Profits, and CLCE rows in FOAPAL table

function calcTotal(){
    console.log("Called to function calcTotal()...");
    var ttl=0;
    if (document.getElementById("sofc_amnt").value != "") {
       console.log(document.getElementById("sofc_amnt").value);
       ttl += parseFloat(document.getElementById("sofc_amnt").value);
    }
    if (document.getElementById("profit_amnt").value != "") {
       console.log(document.getElementById("profit_amnt").value);
       ttl += parseFloat(document.getElementById("profit_amnt").value);
    }
    if (document.getElementById("clce_amnt").value != "") {
       console.log(document.getElementById("clce_amnt").value);
       ttl += parseFloat(document.getElementById("clce_amnt").value);
    }
    var newTtl = (ttl).toFixed(2);
    console.log("the new total is " + newTtl);
    $("#ttl_amnt").text(newTtl);
    $("#ttl").val(newTtl);

};


// ----------
// Function called to add another receipt to upload

function addReceipt() {
    console.log("Called to function addReceipt...");

    // gets current number of receipts    
    var n = parseInt(document.getElementById("numReceipts").value)+1;
    var newName = "receipt"+n;
    $("#receipts").append('<input type="file" class="receipts" name='+newName+' required>');

    // updates number of receipts in hidden value
    $("#numReceipts").val(n);
    console.log("finished adding receipt upload.");
};


// ----------
// Function called to remove a receipt
// Only removes list if there is more than one 

function removeReceipt(){
    console.log("Called to function removeReceipt...");

    // gets current number of receipts and adds one
    var n = parseInt(document.getElementById("numReceipts").value);
    console.log("There are "+n+" file inputs in receipts div");

    // if there is at least one receipt file input
    if (n>1){

	// removes the last dynamically added file input
	$("#receipts input").last().remove();

	// updates number of receipts
	$("#numReceipts").val(n-1);
	console.log("finished removing receipt upload.");
    }
};


// ----------
// Function called to add another list of attendees to upload

function addAttendees() {
    console.log("Called to function addAttendees...");

    // gets current number of attendees lists and adds one
    var n = parseInt(document.getElementById("numAttendees").value)+1;
    var newName = "attendees"+n;
    $("#listattendees").append('<input type="file" class="listattendees" name='+newName+' required>');

    // updates number of attendees lists
    $("#numAttendees").val(n);
    console.log("finished adding list of attendees upload.");
};


// ----------
// Function called to remove the most recently added list of attendees
// Only removes list if there is more than one 

function removeAttendees(){
    console.log("Called to function removeAttendees...");

    // gets current number of attendees lists
    var n = parseInt(document.getElementById("numAttendees").value);
    console.log("There are "+n+" file inputs in listattendees div");

    // if there is more than one file input 
    if (n>1){

	// removes the last dynamically added file input      
        $("#listattendees input").last().remove();

	// updates number of attendees lists
	$("#numAttendees").val(n-1);
        console.log("finished removing listattendees upload.");
    }
};



<?php

// Programming Project#1 for class CS36360
// 	Author: Xiang Lin(xxl180009)
// 	Date: March 01, 2019
// 	Description: Create a Library management application for retrieveing, modify data with mysql database.
//     File Description: check in the book.

 //connect to library database.
 include('connectDB.php');

 if(isset($_GET['isbn'])){
    $isbn = $_GET['isbn'];
}else{
    echo "server does not get data successfully. </br>";
}

$sqlCheckBookInTheLoan = " SELECT * FROM BOOK_LOANS where Isbn ='$isbn'AND Date_in IS NULL;";
$sqlCheckBookInTheLoanResult = mysqli_query($link,$sqlCheckBookInTheLoan)or die("bad query1" . mysqli_error($link));

$checkInDate = date("Y-m-d");



if(mysqli_num_rows($sqlCheckBookInTheLoanResult) == 1){

    $sqlCheckIn = "UPDATE BOOK_LOANS SET Date_in = '$checkInDate' WHERE Isbn = '$isbn'";
    $sqlCheckInResult = mysqli_query($link,$sqlCheckIn) or die("bad query2" . mysqli_error($link));
    echo"book is checked in successfully!";

}else{
    //if the book is not in the loan table
    echo("book is not checked out. Re-do the search to update the availablilty.");
}

//close db
if(!mysqli_close($link)){
    echo "database did not closed successfully";
}
?>
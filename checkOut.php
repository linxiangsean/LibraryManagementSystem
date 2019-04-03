

<?php

// Programming Project#1 for class CS36360
// 	Author: Xiang Lin(xxl180009)
// 	Date: March 01, 2019
// 	Description: Create a Library management application for retrieveing, modify data with mysql database.
//     File Description: check out the book.

    date_default_timezone_set('America/Chicago');
    //connect to library database.
    include('connectDB.php');

    //declear vars
    $isbn = $id = "";

    if(isset($_POST['isbn']) && isset($_POST['id'])){
        $isbn = $_POST['isbn'];
        $id = $_POST['id'];
    }else{
        echo "server does not get data successfully. </br>";
    }

    //get todays date and due date of this check out:
    $checkOutDate = date("Y-m-d");
    $d = strtotime( "+14 Days");
    $dueDate = date("Y-m-d",$d);


    //check is the id exist in the db, if no, return error message, else go head.
        $sqlcheckID = "SELECT * from BORROWER where Card_id = '$id';";
        $sqlcheckIDResult = mysqli_query($link,$sqlcheckID)or die("bad query1" . mysqli_error($link));;
    //check if the borrower already has three books borrowed with no date_in, if yes, return error, else go ahead.
        $sqlcheckNumLoans = "SELECT * from BOOK_LOANS where Card_id = '$id' and Date_in is NULL ;";
        $sqlcheckNumLoansResult = mysqli_query($link,$sqlcheckNumLoans)or die("bad query2" . mysqli_error($link));
    //check if the book is already borrowed:
        $sqlcheckbook = "SELECT * from BOOK_LOANS where Isbn = '$isbn' AND Date_in IS NULL;";
        $sqlcheckbookResult = mysqli_query($link,$sqlcheckbook)or die("bad query3" . mysqli_error($link));

    

        if(mysqli_num_rows($sqlcheckIDResult) == 0){
            echo " The borrower id does not exist, go to add this new borrower.";
        }else if (mysqli_num_rows($sqlcheckNumLoansResult) >= 3){
            echo "Each borrower can have maximum 3 books, over maximum.";
        }else if (mysqli_num_rows($sqlcheckbookResult) >=1 ){
            echo "Book is no more available. Please do the search again to update the availablility";
        }else{
           
            //INSERT BOOK_LOAN tuple.
            $sqlCreateLoan = "INSERT INTO BOOK_LOANS (isbn, card_id, date_out, due_date)
            VALUES ('$isbn','$id','$checkOutDate','$dueDate');";
            $sqlCreateLoanResult = mysqli_query($link,$sqlCreateLoan)or die("bad query4" . mysqli_error($link));

            echo "Book is checked out correctly.";
        }





    //close db
    if(!mysqli_close($link)){
        echo "database did not closed successfully";
    }
?>
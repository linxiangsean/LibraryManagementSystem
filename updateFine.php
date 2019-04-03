
<?php

// <!-- 
// Programming Project#1 for class CS36360
// 	Author: Xiang Lin(xxl180009)
// 	Date: March 01, 2019
// 	Description: Create a Library management application for retrieveing, modify data with mysql database.
//     File Description: performs the update fine table and paid performance.
//  -->
date_default_timezone_set('America/Chicago');
//connect to library database.
include('connectDB.php');

$sqlRefreshFineTable = $cardId ="";

//Calculate fines with books has returned
    //book loans have returned.
    $sqlLoansReturned = "SELECT * from BOOK_LOANS where Date_in is not null;";
    $sqlLoansReturnedResults = mysqli_query($link,$sqlLoansReturned) or die("bad query" . mysqli_error($link));
    while($row = mysqli_fetch_array($sqlLoansReturnedResults)){

        
        if($row['Date_in'] >$row['Due_date']){
            
            $rowDateIn = $row['Date_in'];
            $rowDateDue = $row['Due_date'];

             $dateIn =strtotime($row['Date_in']);
             $dateDue = strtotime($row['Due_date']);
             $date_diff = $dateIn - $dateDue;
             $date_diff = abs(round($date_diff/86400));
             $LoanID =$row['Loan_id'];
             $amount = $date_diff * 0.25;

             if(isset($_GET['cardId'])){

                $cardId = $_GET['cardId'];

                
                if($row['Card_id'] == $cardId){
                    
                    $sqlInsertFine = "INSERT INTO FINES VALUES('$LoanID', '$amount','1');";
                    //remove the current record and then replace.
                    $removeThisRow = "DELETE from FINES where Loan_id ='$LoanID';";
                    $removeThisRowResults = mysqli_query($link,$removeThisRow) or die("bad query" . mysqli_error($link));
                    //REMOVED GOOD.

                    echo"<p id = 'fineMessage'>Fine paid successfully with borrower id: ".$cardId."</p>";
                }else{
                  
                    if($row['Card_id'] == $cardId){echo"<p id = 'fineMessage'>The borrower with this ID is not in the fine table:  ".$cardId."</p>";}
                    continue;
                }
            }
            else
            {
                $sqlInsertFine = "INSERT INTO FINES VALUES('$LoanID', '$amount','0');";
            }

            
            if(!mysqli_query($link,$sqlInsertFine)){
                continue;
            }
            //$sqlInsertFineResult= mysqli_query($link,$sqlInsertFine) or die("bad query2" . mysqli_error($link));
        }
    }

    //calculate fines with books has not returned
        //book loans have not returned.
            $sqlLoansNotReturned = "SELECT * from BOOK_LOANS where Date_in is null;";
            $sqlLoansNotReturnedResults = mysqli_query($link,$sqlLoansNotReturned) or die("bad query" . mysqli_error($link));
            $today = date('Y-m-d');
           
            while($row = mysqli_fetch_array($sqlLoansNotReturnedResults)){

                if($today >$row['Due_date']){
                    
                    $rowDateDue = $row['Due_date'];
        
                     $dateToday =strtotime($today);
                     $dateDue = strtotime($row['Due_date']);
                     $date_diff = $dateToday - $dateDue;
                     $date_diff = abs(round($date_diff/86400));
                    
                     $LoanID =$row['Loan_id'];
                     $amount = $date_diff * 0.25;


                     if(isset($_GET['cardId'])){
                        $cardId = $_GET['cardId'];
                       
                        
    
                        if($row['Card_id'] == $cardId){
                           
                            // $sqlInsertFine = "INSERT INTO FINES VALUES('$LoanID', '$amount','1');";
                            // //remove the current record and then replace.
                            // $removeThisRow = "DELETE from FINES where Loan_id ='$LoanID';";
                            // $removeThisRowResults = mysqli_query($link,$removeThisRow) or die("bad query" . mysqli_error($link));
                            //REMOVED GOOD.
                            
                            echo "<p id = 'fineMessage'>Please Return the book with ISBN: ". $row['Isbn'] ." before paying the fine!!!</p>";
                            break;

                            //echo"<p id = 'fineMessage'>Fine paid successfully with borrower id: ".$cardId."</p>";
                         
                        }else{
                            if($row['Card_id'] == $cardId){echo"<p id = 'fineMessage'>The borrower with this ID is not in the fine table:  ".$cardId."</p>";}
                            continue;
                        }
                    }else{
                        $sqlInsertFine = "INSERT INTO FINES VALUES('$LoanID', '$amount','0');";
                    }
               

                    if(!mysqli_query($link,$sqlInsertFine)){
                        continue;
                    }
                    //$sqlInsertFineResult= mysqli_query($link,$sqlInsertFine) or die("bad query2" . mysqli_error($link));
                }
            }

        $sqlPrintFine = "SELECT Card_id, Fine_amt,Paid from FINES JOIN BOOK_LOANS WHERE FINES.Loan_id = BOOK_LOANS.Loan_id;";

        $sqlPrintFineResults = mysqli_query($link,$sqlPrintFine) or die("bad query" . mysqli_error($link));

        echo"<table id = 'fineTable'><tr><th>Card id</th><th>Fine Amount</th><th>Paid</th></tr>";
    while($row = mysqli_fetch_array($sqlPrintFineResults)){
        echo"<tr><td>".$row['Card_id']."</td><td>".$row['Fine_amt']."</td><td>".$row['Paid']."</td></tr>";
    }

    echo"</table>";

if(!mysqli_close($link)){
    echo "database did not closed successfully";
}

?>


<?php
// <!-- 
// Programming Project#1
// 	Author: Xiang Lin(xxl180009)
// 	Date: March 01, 2019
// 	Description: Create a Library management application for retrieveing, modify data with mysql database.
//     File Description: create the new borrowers.
//  -->

//connect to library database.
include('connectDB.php');

echo "<p id= 'BorrowerMessage'>";

// echo "user name is: ", $name ,"</br>";
// echo "user ssn is: ", $ssn ,"</br>";
// echo "user addr is: ", $addr ,"</br>";
// echo "user phone is: ", $phone ,"</br>";

//initialize data
$id = $name = $ssn = $addr = $phone = "";

//get user's input
if(isset($_GET['userName']) && isset($_GET['userSsn'])&& isset($_GET['userAddress'])&&isset($_GET['userPhone']) ){
    $name = $_GET['userName'];
    $ssn = $_GET['userSsn'];
    $addr = $_GET['userAddress'];
    $phone = $_GET['userPhone'];

    $name = mysqli_real_escape_string($link,$name);
    $ssn = mysqli_real_escape_string($link,$ssn);
    $addr = mysqli_real_escape_string($link,$addr);
    $phone = mysqli_real_escape_string($link,$phone);
}else{
    echo "server does not get data successfully. </br>";
}


//make a sql call to retrieve the current highest Card_id in the database
$sql_top_id = "SELECT Card_id  FROM BORROWER ORDER BY Card_id DESC LIMIT 0, 1;";
$top_id_result = mysqli_query($link,$sql_top_id) or die("bad query2" . mysqli_error($link));
$current_top_id_asArray = mysqli_fetch_array($top_id_result);
$current_top_id = $current_top_id_asArray['Card_id'];
//echo "current max id in the db is: ", $current_top_id ,"</br>";

//give user a new card_id number
$id = (int)$current_top_id + 1;
//echo "user assigned new id is: ", $id ,"</br>";

//print out error message if name and ssn is already existed in the db.
//query with input name and ssn, return empty if there is no match, if there is a match, print out error message.
$sql_search = "SELECT * from BORROWER where Bname = '$name' or Ssn = '$ssn';";
$searchResult = mysqli_query($link,$sql_search) or die("bad query3" . mysqli_error($link));
//echo("num of rows in the current system with same name and ssn: ". mysqli_num_rows($searchResult));
if(mysqli_num_rows($searchResult) > 0){
    echo "<p id='BduplicateError'>The Name or SSN is already existed. Enter a new borrower.</p>";
}else{

    //we can do insert if there is no match

    $sql_insert = "INSERT INTO BORROWER
                    VALUES ('$id','$ssn','$name','$addr','$phone');";

    $insert_OK = mysqli_query($link,$sql_insert) or die("bad query4" . mysqli_error($link));

    echo("<p id='Bsuccess'>New borrower generated successfully!<br/><br/>And new borrower's Card ID is: <b>". $id. "</b></p>");

}

echo "</p> ";

if(!mysqli_close($link)){
    echo "database did not closed successfully";
}

?>




<?php

// <!-- 
// Programming Project#1 for class CS36360
// 	Author: Xiang Lin(xxl180009)
// 	Date: March 01, 2019
// 	Description: Create a Library management application for retrieveing, modify data with mysql database.
//     File Description: performs the main search queries.
//  -->
 
echo "<table id = 'outputTable'>
<tr class ='header'><th>ISBN</th><th>TITLE</th><th>Author Name</th><th>Availability</th><th>Option</th></tr>";

//connect to library database.
include('connectDB.php');
//declear the target search string.
$search_string = "";

//get user's string
if(isset($_GET['string'])){
    $search_string = $_GET["string"];
}else{
    echo "server does not get data </br>";
}
//$search_string = $_POST["q"];


$search_string = mysqli_real_escape_string($link,$search_string);



//put search string into ISBN search sql
//if returns something, get it
//else do a normal search
$ISBNNumQuery = "SELECT BOOK.Title
FROM BOOK
WHERE ISBN = '$search_string'";

$ISBNNumQueryResult = mysqli_query($link,$ISBNNumQuery) or die("bad query0" . mysqli_error($link));



if(mysqli_num_rows($ISBNNumQueryResult) > 0){
    echo "Searched for: ", $search_string, "<br/>";
    $ISBNTitle = mysqli_fetch_array($ISBNNumQueryResult);
    $search_string = $ISBNTitle['Title'] ;
    echo "Book title is:",$search_string,"<br/>";
}else{  
    echo "Searched for: ", $search_string, "<br/>";
}



$bookLoanEmpty = TRUE;
$checkBookLoan = "SELECT * from BOOK_LOANS where Date_in is null;";
$checkBookLoanResult = mysqli_query($link,$checkBookLoan) or die("bad query1" . mysqli_error($link));



if(mysqli_num_rows($checkBookLoanResult)>0){
    $bookLoanEmpty = FALSE;
}

$searchQuery = " SELECT distinct BIG.Isbn, Big.Title, Big.Name,case when SMALL.LoanCount= 0 then '0' else '1' end as Availability
FROM
(
SELECT distinct tablue.Isbn,tablue.Title,tablue.Name
FROM
    (
      (SELECT Bookb.Isbn, BOOKb.Title, AUTHORS.name
        FROM (
            SELECT BOOKa.ISBN , BOOKa.Title, BOOK_AUTHORS.Author_id
              FROM (SELECT BOOK.Isbn, BOOK.Title
                    FROM BOOK
                    WHERE BOOK.Title LIKE'%$search_string%' ) AS BOOKa
                    JOIN BOOK_AUTHORS ON BOOK_AUTHORS.Isbn = BOOKa.ISBN) AS BOOKb
              JOIN AUTHORS
              ON BOOKb.Author_id = AUTHORS.Author_id)
UNION ALL
      (SELECT BOOK2.Isbn,BOOK.Title,BOOK2.Name
              FROM (SELECT BOOK_AUTHORS.Isbn, BOOK1.Author_id , BOOK1.Name
                      FROM (SELECT Author_id, AUTHORS.Name
                            FROM AUTHORS
                            WHERE AUTHORS.Name LIKE '%$search_string%') AS BOOK1
                      JOIN BOOK_AUTHORS ON BOOK_AUTHORS.Author_id = BOOK1.Author_id) AS BOOK2
              JOIN BOOK ON BOOK2.Isbn = BOOK.Isbn)
    ) as tablue ORDER BY tablue.Isbn
) AS BIG
LEFT JOIN
(
  select table2.isbn, LoanCount
  from
  (select distinct BOOK_AUTHORS.isbn ,case when BOOK_AUTHORS.isbn= BOOK4.ISBN then '0' else '1' end   AS LoanCount from (
SELECT BOOK3.Isbn, COUNT(BOOK3.Isbn) as counter
FROM (
SELECT Isbn, BOOKT.Title, AUTHORS.name
  FROM (
      SELECT BOOK1.ISBN , BOOK1.Title, BOOK_AUTHORS.Author_id
         FROM (SELECT BOOK.Isbn, BOOK.Title
              FROM BOOK
               ) AS BOOK1
                JOIN BOOK_AUTHORS ON BOOK_AUTHORS.Isbn = BOOK1.ISBN) AS BOOKT
          JOIN AUTHORS ON BOOKT.Author_id = AUTHORS.Author_id ORDER BY Isbn
) AS BOOK3
JOIN BOOK_LOANS ON BOOK3.Isbn = BOOK_LOANS.Isbn and BOOK_LOANS.Date_in IS NULL GROUP BY BOOK3.Isbn
    ) as BOOK4
    join BOOK_AUTHORS order by isbn)as table2
where LoanCount =0
  ) AS SMALL
ON BIG.Isbn = SMALL.Isbn;";

//set a counter for counting results.
$book_found = 0;
//get the result of the query
$result = mysqli_query($link,$searchQuery) or die("bad query2" . mysqli_error($link));

//when bookLoan table is not empty, handle differently for availability
if(!$bookLoanEmpty)
{
    //go through the output and find rows with same Isbn
    //which means the book has multiple authors
    //then, concat them in to temp_name.
    $prev = mysqli_fetch_array($result);//(first) prev row is stored
    while($thisRow = mysqli_fetch_array($result))//(second) this row is stored
    {
        $temp_name = $prev['Name'];//prev row's author_name
        while($thisRow['Isbn']==$prev['Isbn']) //if the prev row is the same record as this row
            {
                $temp_name=$temp_name.", ".$thisRow['Name']; //concat this row author to prev row author.
                $prev = $thisRow; //change prev row to this row
                $thisRow = mysqli_fetch_array($result); // change this row to next row
                //go through the while loop 
                //check if next row has the same Isbn with this row
                //if yes, concat to temp_name
                //else go print out temp_name
            }
        //if this row is not the same book with prev row
        //print out prev row
        printOutTableRow($prev['Isbn'], $prev['Title'], $temp_name, $prev['Availability']);
        $book_found = $book_found+1;
        //let prev row be this row and do next iteration
        $prev =$thisRow;
    }
    // handle last row.
    if(!empty($prev['Isbn'])){
        printOutTableRow($prev['Isbn'], $prev['Title'], $prev['Name'], $prev['Availability']);
        $book_found = $book_found+1;
    }

}
else //no record in BOOK_LOAN, books' availability are always 1.
{
    //go through the output and find rows with same Isbn
    //which means the book has multiple authors
    //then, concat them in to temp_name.
    $prev = mysqli_fetch_array($result);//(first) prev row is stored
    while($thisRow = mysqli_fetch_array($result))//(second) this row is stored
    {
        $temp_name = $prev['Name'];//prev row's author_name
        while($thisRow['Isbn']==$prev['Isbn']) //if the prev row is the same record as this row
            {
                $temp_name=$temp_name.", ".$thisRow['Name']; //concat this row author to prev row author.
                $prev = $thisRow; //change prev row to this row
                $thisRow = mysqli_fetch_array($result); // change this row to next row
                //go through the while loop 
                //check if next row has the same Isbn with this row
                //if yes, concat to temp_name
                //else go print out temp_name
            }
        //if this row is not the same book with prev row
        //print out prev row
        printOutTableRow($prev['Isbn'], $prev['Title'], $temp_name, '1');
        $book_found = $book_found+1;
        //let prev row be this row and do next iteration
        $prev =$thisRow;
    }
    // handle last row.
    if(!empty($prev['Isbn'])){
        printOutTableRow($prev['Isbn'], $prev['Title'], $prev['Name'], '1');
        $book_found = $book_found+1;
    }
}

if($book_found > 1){
    echo "<p id = 'resultMessage'>", $book_found, " books are found in the database </p>";
}else if($book_found == 1){
    echo  "<p id = 'resultMessage'> 1 book is found in the database </p>";
}
else{
    echo  "<p id = 'resultMessage'> 0 book is found in the database </p>";
}

//helper function to print out table row with correct content.
function printOutTableRow($Isbn,$Title,$AuthorName,$Availability){
        
        if($Availability == 1){ //book is available for borrow
            $output = '<tr>
                    <td>'. $Isbn . '</td>
                    <td>'. $Title . '</td>
                    <td>'. $AuthorName . '</td>
                    <td>'.$Availability.'</td> 
                    <td><button type="button" class = "availableBtn" name = "'.$Isbn.'">Check Out</button></td>
                    </tr>';
        }else{
            $output = '<tr>
                    <td>'. $Isbn . '</td>
                    <td>'. $Title . '</td>
                    <td>'. $AuthorName . '</td>
                    <td>'.$Availability.'</td> 
                    <td><button type="button" class = "notavailableBtn" name = "'.$Isbn.'">Check In</button></td>
                    </tr>';
        }

        
        echo $output;
}

if(!mysqli_close($link)){
    echo "database did not closed successfully";
}
echo "</table>";
echo "<script>
    $('#resultMessage').css('color', 'green');
</script>";
?>


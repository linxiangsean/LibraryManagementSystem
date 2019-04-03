

<?php
// <!-- 
// Programming Project#1 for class CS36360
// 	Author: Xiang Lin(xxl180009)
// 	Date: March 01, 2019
// 	Description: Create a Library management application for retrieveing, modify data with mysql database.
//     File Description: connect to the database, user can change the stats here to match their machine.
//  -->

   $user = 'root';
   $password = 'root';
   $db = 'LIBRARY';
   $host = 'localhost';
   $port = 8889;

   $link = mysqli_init();
   $con = mysqli_real_connect(
      $link,
      $host,
      $user,
      $password,
      $db,
      $port
   );

   if(!$con){
      die("database connection failed: " . mysqli_error($link));
   }
?>
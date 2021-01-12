<?php 

  session_start();
  
  if(!isset($_SESSION['loggedin']) || ($_SESSION['loggedin']!=true))
  {
     header("location: HomePage.php");
  }

  include '_dbconnect.php';
  
  if(isset($_GET['q']))
  {
     $sno = $_GET['q'];
     $sql = "SELECT * FROM `updates` WHERE `updates`.`sno` = '$sno' ";
     $result = mysqli_query($conn,$sql);
     $row = mysqli_fetch_assoc($result);

     echo $row['cust_desc'];
  }

  ?>
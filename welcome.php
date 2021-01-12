<?php 

  session_start();
  
  if(!isset($_SESSION['loggedin']) || ($_SESSION['loggedin']!=true))
  {
     header("location: HomePage.php");
  }

  include '_dbconnect.php';
  $show_error = "";

  $is_insertion = false;
  $is_update    = false;
  $is_delete    = false;
  $email = $_SESSION['email'];
  if(isset($_GET['delete']))
  {
           $sno = $_GET['delete'];
           $sql = "DELETE FROM `customer` WHERE `cust_id` = $sno";
           $result = mysqli_query($conn,$sql);
           
           if($result)
            $is_delete = true;
           else 
            $show_error = "Something Went Wrong! Customer could not be deleted";
          
  }

  if( $_SERVER['REQUEST_METHOD'] == 'POST')
  {
    if(isset($_POST['snoEdit']))
     {
       // update the current balance
           $cust_id = $_POST['snoEdit'];
           $credit = $_POST['creditEdit'];
           $debit = $_POST['debitEdit'];
           $description = $_POST['descEdit'];
           if(!$description) $description="Empty";
           $admin = $_SESSION['admin_id'];

           $sql = "INSERT INTO `updates` (`admin_id`, `cust_id`,`cust_credit`, `cust_debit`, `cust_desc`) VALUES ('$admin', '$cust_id','$credit','$debit','$description')";
           $result = mysqli_query($conn,$sql);

            if($result){
                $sql = "SELECT * FROM `customer` WHERE `cust_id` = $cust_id";
                $result = mysqli_query($conn,$sql);
                $row = mysqli_fetch_assoc($result);
               // echo $row["cust_balance"];
                if(!$credit) $credit = 0;
                if(!$debit)  $debit = 0;
               $new_bal = $row["cust_balance"] + $debit - $credit;
                
           $sqlupd = "UPDATE `customer` SET `cust_balance` = '$new_bal' WHERE `customer`.`cust_id` = $cust_id";
           $resultupd = mysqli_query($conn,$sqlupd);
          }

           if($result)
           $is_update = true;
           else $show_error = "Something Went Wrong! New Entry could not be added";
   }
    else{
    $name = $_POST["nameEdit"];
    $contact = $_POST["contactEdit"];
    $balance = $_POST["balanceEdit"];
    $address = $_POST["addressEdit"];
    $admin = $_SESSION['admin_id'];

    $sql = "SELECT * FROM `customer` WHERE `customer`.`cust_contact_no` = '$contact'";
    $result = mysqli_query($conn,$sql);
    $num = mysqli_num_rows($result);

   if($num==0) {
    $sql = "INSERT INTO `customer` (`admin_id`, `cust_name`,`cust_contact_no`, `cust_balance`, `cust_address`) VALUES ('$admin', '$name','$contact','$balance','$address')";
    $result = mysqli_query($conn,$sql);
    
    $sql = "SELECT * FROM `customer` WHERE `customer`.`cust_contact_no` = '$contact'";
    $result = mysqli_query($conn,$sql);
    $row = mysqli_fetch_assoc($result);
    

    $cust_id = $row['cust_id'];
    $credit = 0;
    $cust_desc = "Opening Balance";

    $sql = "INSERT INTO `updates` (`admin_id`,`cust_id`,`cust_credit`, `cust_debit`,`cust_desc` ) VALUES ('$admin', '$cust_id','$credit','$balance','$cust_desc')";
    $result = mysqli_query($conn,$sql);
    

    if($result)
      $is_insertion = true;
    else 
      $show_error = "Something went wrong! New Customer could not be added";
   
  }
   else 
      $show_error = "Phone number already registered! Use another one";

   }
  }
?>

<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
    <link href="//cdn.datatables.net/1.10.22/css/jquery.dataTables.min.css" rel="stylesheet">


    <title>Welcome! <?php echo $_SESSION['UserName'] ?></title>
     <style>
       #descript{
               text-align: center;
       }
     </style>
  </head>
  <!-- Here Body Starts -->
  <body>
      <!-- Button trigger modal -->
 <!-- <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
  Launch demo modal
</button>  -->

<!-- Edit Modal for new customer -->
<div class="modal fade" id="newModal" tabindex="-1" aria-labelledby="newModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="newModalLabel">Add new Customer</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">  <!-- here modal body starts -->
        <form action="/Garg Enterprises/welcome.php" method="POST">
  <div class="mb-3">
    <label for="name" class="form-label">Name</label>
    <input type="text" class="form-control" id="nameEdit" name="nameEdit" aria-describedby="emailHelp">
  </div>
  <div class="mb-3">
    <label for="contact" class="form-label">Contact No.</label>
    <input type="text" class="form-control" id="contactEdit" name="contactEdit" aria-describedby="emailHelp">
  </div>
  <div class="mb-3">
    <label for="balance" class="form-label">Opening Balance</label>
    <input type="number" class="form-control" id="balanceEdit" name="balanceEdit" aria-describedby="emailHelp">
  </div>
  <div class="mb-3">
  <label for="address" class="form-label">Address</label>
  <textarea class="form-control" id="addressEdit" name="addressEdit" rows="4"></textarea>
</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Add Customer</button>
      </div>
    </form> 
    </div>
  </div>
</div>

<!-- Edit Modal for new entry -->
<div class="modal fade" id="entryModal" tabindex="-1" aria-labelledby="entryModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="entryModalLabel">Add New Entry</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">  <!-- here modal body starts -->
        <form action="/Garg Enterprises/welcome.php" method="POST">
          <input type="hidden" name="snoEdit" id="snoEdit">
  <div class="mb-3">
    <label for="credit" class="form-label">Amount paid</label>
    <input type="number" class="form-control" id="creditEdit" name="creditEdit" aria-describedby="emailHelp">
  </div>
  <div class="mb-3">
    <label for="debit" class="form-label">Amount to Pay</label>
    <input type="number" class="form-control" id="debitEdit" name="debitEdit" aria-describedby="emailHelp">
  </div>
  <div class="mb-3">
  <label for="desc" class="form-label">Description</label>
  <textarea class="form-control" id="descEdit" name="descEdit" rows="4"></textarea>
  </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Add</button>
      </div>
      </form> 
    </div>
  </div>
</div>


       <!--  Navigation bar  -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Garg Enterprises</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0 ">

        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="#">Home</a>
        </li>

        <li class="nav-item">
          <a class="nav-link" href="#">About</a>
        </li>

        <li class="nav-item">
          <a class="nav-link" href="#">Contact us</a>
        </li>

        <li class="nav-item">
          <a class="nav-link" href="logout.php">Logout</a>
        </li>
  
        <li class="nav-item" >
          <button type="button" class="new_cust nav-link btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
            Add new Customer
          </button>
        </li>

      </ul>

     
    </div>
  </div>
  </nav>

  <?php 

  if($show_error)
  {
    echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">
  '.$show_error.'
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>';
  }
  else  if($is_insertion)
    {
      echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
  <strong>Success!</strong> Your Customer has been inserted successfully
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>';
    }
    else if($is_update)
      {
      echo '<div class="alert alert-primary alert-dismissible fade show" role="alert">
  <strong>Success!</strong> Your Entry has been added successfully
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>';
    }
    else if($is_delete)
      {
      echo '<div class="alert alert-primary alert-dismissible fade show" role="alert">
  <strong>Success!</strong> Your customer has been deleted successfully
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>';
    }
   ?>
 
   <div>
     <h1 style="text-align: center; padding-top: 5%;"> Customers List </h1>
   </div>
  
   <!-- datatable -->
  <div class="container my-4">
   
      <table class="table" id="myTable">
  <thead>
    <tr>
      <th scope="col">S.No</th>
      <th scope="col">Name</th>
      <th scope="col" id="descript">Contact No.</th>
      <th scope="col">Balance (in Rs)</th>
      <th scope="col">Actions</th>
    </tr>
  </thead>
  <tbody>

     <?php 
         
         $sql = "SELECT * From `customer` ";
         $result = mysqli_query($conn,$sql);
         $sno = 0;
         while($row = mysqli_fetch_assoc($result))
         {
          if($row['admin_id'] == $_SESSION['admin_id']){
          $sno++;
          echo "<tr>
               <th scope='row'>".$sno ."</th>
               <td>".$row['cust_name'] ."</td>
               <td>".$row['cust_contact_no'] ."</td>
               <td>".$row['cust_balance'] ."</td>
              <td> 
                <div class='container'>
                <button class='entry btn btn-primary btn-sm' id=".$row['cust_id'].">New Entry</button>
                <button class='delete btn btn-primary btn-sm' id=d".$row['cust_id'].">Delete Customer</button>
                <button class='details btn btn-primary btn-sm' id=e".$row['cust_id'].">See Details</button>
              </div>  
              </td>
               </tr>";
             }
         }
   ?>
 
  </tbody>
  </table>
  </div>
  <hr>
    <!-- Optional JavaScript; choose one of the two! -->

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>
    <script
      src="https://code.jquery.com/jquery-3.5.1.js"
      integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc="
      crossorigin="anonymous"></script>
    <script src="//cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
    <script>
      $(document).ready( function () {
      $('#myTable').DataTable();
        } );
    </script>
    <script>
      new_cust = document.getElementsByClassName('new_cust');
      Array.from(new_cust).forEach((element) => {
        element.addEventListener("click",(e)=>{
          var myModal = new bootstrap.Modal(document.getElementById('newModal'), {
          keyboard: false
          })
          myModal.toggle(); 
          // model script end
        })
      })
      </script>
    <script>
      entry = document.getElementsByClassName('entry');
      Array.from(entry).forEach((element) => {
        element.addEventListener("click",(e)=>{
          console.log("entry ",e.target.parentNode.parentNode.parentNode);
          tr = e.target.parentNode.parentNode.parentNode;
          cust_name = tr.getElementsByTagName("td")[0].innerText;
          snoEdit.value = e.target.id;
          console.log(snoEdit.value);
          console.log(cust_name);
          // Modal script
          var myModal = new bootstrap.Modal(document.getElementById('entryModal'), { 
            //
          keyboard: false
          })
          document.getElementById("entryModalLabel").innerHTML = `New Entry For ${cust_name} `;
          myModal.toggle(); 
          // model script end
        })
      })

         deletes = document.getElementsByClassName('delete');
        Array.from(deletes).forEach((element) => {
        element.addEventListener("click",(e)=>{
          console.log("delete ",);
          sno = e.target.id.substr(1,);
          
          if(confirm("Do you want to delete this note ?"))
          {
             window.location = `/Garg Enterprises/welcome.php?delete=${sno}`;
          }

          })
      })

      details = document.getElementsByClassName('details');
        Array.from(details).forEach((element) => {
        element.addEventListener("click",(e)=>{
          console.log("details ",);
          sno = e.target.id.substr(1,);
          window.location = `/Garg Enterprises/customer.php?sno=${sno}`;
          
          })
      })
    </script>

    <!-- Option 2: Separate Popper and Bootstrap JS -->
    <!--
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js" integrity="sha384-q2kxQ16AaE6UbzuKqyBE9/u/KzioAlnx2maXQHiDX9d4/zp8Ok3f+M7DPm+Ib6IU" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.min.js" integrity="sha384-pQQkAEnwaBkjpqZ8RU1fF1AKtTcHJwFl3pblpTlHXybJjHpMYo79HY3hIi4NKxyj" crossorigin="anonymous"></script>
    --> 
  </body>
</html>
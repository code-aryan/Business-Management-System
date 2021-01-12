<?php 

     session_start();

  if(!isset($_SESSION['loggedin']) || ($_SESSION['loggedin']!=true))
  {
     header("location: HomePage.php");
  }

  include '_dbconnect.php';

  // $is_insertion = false;
     $is_update    = false;
     $is_delete    = false; 
     $show_error = "";
  if(isset($_GET['sno']))
  {
           $sno = $_GET['sno'];
           $_SESSION['cust_id'] = $sno;
  }

  if( $_SERVER['REQUEST_METHOD'] == 'POST')
  {
     if(isset($_POST['snodelete']))
     {
       // Delete the entry
           $sno = $_POST['snodelete'];
         
           $sql = "SELECT * FROM `updates` WHERE `sno` = $sno";
           $result = mysqli_query($conn,$sql);
           $row = mysqli_fetch_assoc($result);

           $credit = $row['cust_credit'];
           $debit  = $row['cust_debit'];
           $cust_id = $row['cust_id'];

           $sql = "SELECT * FROM `customer` WHERE `cust_id` = $cust_id";
           $result = mysqli_query($conn,$sql);
           $row = mysqli_fetch_assoc($result);
          
           if(!$credit) $credit = 0;
           if(!$debit)  $debit = 0;

           $new_bal = $row["cust_balance"] - $debit + $credit;

           $sql = "UPDATE `customer` SET `cust_balance` = '$new_bal' WHERE `customer`.`cust_id` = $cust_id";
           $result = mysqli_query($conn,$sql);

           $sql = "DELETE FROM `updates` WHERE `sno` = $sno";
           $result = mysqli_query($conn,$sql);
           if($result)
           $is_delete = true;
           else $show_error = "Something Went Wrong! Entry could not be deleted";  
   }
   else if(isset($_POST['snoEdit']))
   {
        // update the Entry
        $sno =    $_POST['snoEdit'];
        $newcredit = $_POST['creditEdit'];
        $newdebit =  $_POST['debitEdit'];
        $newdesc = $_POST['descEdit'];
           $sql = "SELECT * FROM `updates` WHERE `sno` = $sno";
           $result = mysqli_query($conn,$sql);
           $row = mysqli_fetch_assoc($result);

           $oldcredit = $row['cust_credit'];
           $olddebit  = $row['cust_debit'];
           $cust_id = $row['cust_id'];
           $olddesc = $row['cust_desc'];

           $sql = "SELECT * FROM `customer` WHERE `cust_id` = $cust_id";
           $result = mysqli_query($conn,$sql);
           $row = mysqli_fetch_assoc($result);
          
           if(!$oldcredit) $oldcredit = 0;
           if(!$olddebit)  $olddebit = 0;
           if(!$newcredit) $newcredit = 0;
           if(!$newdebit)  $newdebit = 0;
           if(!$newdesc) $newdesc = $olddesc;

           $new_bal = $row["cust_balance"] - $olddebit + $oldcredit + $newdebit - $newcredit;

           $sql = "UPDATE `customer` SET `cust_balance` = '$new_bal' WHERE `customer`.`cust_id` = $cust_id";
           $result = mysqli_query($conn,$sql);

           $sql = "UPDATE `updates` SET `cust_credit` = '$newcredit',`cust_debit` = '$newdebit',`cust_desc` = '$newdesc'  WHERE `updates`.`sno` = $sno";
           $result = mysqli_query($conn,$sql);


           if($result)
           $is_update = true;
           else $show_error = "Something Went Wrong! Entry could not be edited";
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

<body>
<!-- Edit Modal -->
<div class="modal fade" id="editentryModal" tabindex="-1" aria-labelledby="editentryModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editentryModalLabel">Edit this Entry</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">  <!-- here modal body starts -->
        <form action="#" method="POST">
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
        <button type="submit" class="btn btn-primary">Make Changes</button>
      </div>
      </form> 
    </div>
  </div>
</div>

       <!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteModalLabel">Do You want to delete this entry?</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" style="padding: 0rem">  <!-- here modal body starts -->
        <form action="/Garg Enterprises/customer.php" method="POST">
          <input type="hidden" name="snodelete" id="snodelete">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
        <button type="submit" class="btn btn-primary">Save changes</button>
      </div>
      </form> 
    </div>
  </div>
</div>

       <!-- Description Modal -->
       <div class="modal fade" id="descModal" tabindex="-1" aria-labelledby="descModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="descModalLabel">Description</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" >  <!-- here modal body starts -->
        <p id="descModalBody">Empty</p>
        <form action="/Garg Enterprises/customer.php" method="POST">
          <input type="hidden" name="snodesc" id="snodesc">
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
          <a class="nav-link active" aria-current="page" href="/Garg Enterprises/welcome.php">Home</a>
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
    else if($is_update)
      {
      echo '<div class="alert alert-primary alert-dismissible fade show" role="alert">
  <strong>Success!</strong> Your Entry has been Edited successfully
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>';
    }
    else if($is_delete)
      {
      echo '<div class="alert alert-primary alert-dismissible fade show" role="alert">
  <strong>Success!</strong> Your Entry has been deleted successfully
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>';
    }

   ?>

   <!-- customer details -->
   <div class="container mb-2">
    <h2 style="text-align: center; padding-top: 5%;">Customer details</h2>
    <table class="table" id="">
      <thead>
       <tr>
         <th scope="col">Name</th>
         <th scope="col">Contact Number</th>
         <th scope="col">Balance Left</th>
         <th scope="col">Address</th>
       </tr>
      </thead>
      <tbody>

        <?php 
            $num = $_SESSION['cust_id'];
            $sql = "SELECT * FROM `customer` WHERE `customer`.`cust_id` = $num";
            $result = mysqli_query($conn,$sql);
            $row = mysqli_fetch_assoc($result);

            echo "<tr>
                 <td>".$row['cust_name'] ."</td>
                 <td>".$row['cust_contact_no'] ."</td>
                 <td>".$row['cust_balance'] ."</td>
                 <td>".$row['cust_address'] ."</td>
                 </tr>";
     ?> 
     </tbody>
    </table>

  </div>

      
  <div class = "container">
     <h4 style="text-align: center; padding-top: 5%;"> Transaction history </h4>
   </div>

   <!-- datatable -->
  <div class="container my-4">
   
   <table class="table" id="myTable">
<thead>
 <tr>
   <th scope="col">S.No</th>
   <th scope="col">Date (y-m-d) & Time </th>
   <th scope="col">Amount paid</th>
   <th scope="col">Amount to pay</th>
   
   <th scope="col">Actions</th>
 </tr>
</thead>
<tbody>

   <?php // fetching data for entry table
      
      $sql = "SELECT * From `updates`";
      $result = mysqli_query($conn,$sql);
      $sno = 0;
      while($row = mysqli_fetch_assoc($result))
      {
       if($row['cust_id'] == $_SESSION['cust_id']){
       $sno++;
       echo "<tr>
            <th scope='row'>".$sno ."</th>
            <td>".$row['time'] ."</td>
            <td>".$row['cust_credit'] ."</td>
            <td>".$row['cust_debit'] ."</td>
           <td> 
             <div class='container'>
             <button class='edit btn btn-primary btn-sm' id=".$row['sno'].">Edit</button>
             <button class='delete btn btn-primary btn-sm' id=d".$row['sno'].">Delete</button>
             <button class='description btn btn-primary btn-sm' content=".$row['cust_desc']." id=".$row['sno'].">description</button>
           </div>  
           </td>
            </tr>";
          }
      }
?> 

</tbody>
</table>
</div>

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
      // edit Modal script
      edits = document.getElementsByClassName('edit');
      Array.from(edits).forEach((element) => {
          element.addEventListener("click",(e)=>{
          console.log("edit ",e.target.parentNode.parentNode);
          snoEdit.value = e.target.id;
          console.log(snoEdit.value);
          // Modal script
          var myModal = new bootstrap.Modal(document.getElementById('editentryModal'), {
          keyboard: false
          })
          myModal.toggle(); 
          // model script end
        })
      })
      // delete modal script
          deletes = document.getElementsByClassName('delete');
          Array.from(deletes).forEach((element) => {
          element.addEventListener("click",(e)=>{
          console.log("delete ",);
          snodelete.value = e.target.id.substr(1,);
          console.log(snodelete.value);
          // Modal script
          var myModal = new bootstrap.Modal(document.getElementById('deleteModal'), {
          keyboard: false
          })
          myModal.toggle(); 
          // model script end

          })
      })
      // description modal script using AJAX
          desc = document.getElementsByClassName('description');
          Array.from(desc).forEach((element) => {
          element.addEventListener("click",(e)=>{
            sno = e.target.id;
            //console.log(sno);
            var xhttp;
            xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
            document.getElementById("descModalBody").innerHTML = this.responseText;
            }
            };
            xhttp.open("GET", "getcustomer.php?q="+sno, true);
            xhttp.send();
          // Modal script
          var myModal = new bootstrap.Modal(document.getElementById('descModal'), {
          keyboard: false
          })
          myModal.toggle(); 
          // model script end
          })
      })
    </script>
    
    </body>
 </html>
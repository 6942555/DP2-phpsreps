<?php
  session_start();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../favicon.ico">

    <title>PHP - Create Sale</title>

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- TODO: CUSTOM CSS -->

  </head>

  <body>

    <?php
      require_once("settings.php");
      $conn = @mysqli_connect(
          $host,
          $user,
          $pwd,
          $sql_db
      );

      //TODO: include("template.php");
      

      $sql_table = "Stock";
      $query = "SELECT * FROM $sql_table";
      $result = mysqli_query($conn, $query);
      $result_array = Array();
      while ($row = mysqli_fetch_assoc($result))
      {
        $result_array[] = $row;
      }

      //TODO: convert to json array? 
      
      mysqli_close($conn);
    ?>

    <!--TODO: css in custom -->

    <div class="container custom custom-header">

      <div>
        <h1>Log a New Sale</h1>
        <p class="lead">Please use the form below to create a new sale.</p>
      </div>

    </div>
    <br />
    <div class="container custom">
      <div class="col-md-10 col-md-offset-1">
        <form id = "addNewSale" method = "post" action = "create_sale_process.php" novalidate="novalidate">
            <br />
            <table id="items" class="table table-bordered table-striped">
              <tr>
                <th class="col-md-1">Keycode</th>
                <th class="col-md-1">Quantity</th>
                <th class="col-md-1">Price</th>
                <th class="col-md-1">Total</th>
                <th class="col-md-1">Remove Item</th>
              </tr>
              <tr>
                <th colspan="4">Grand Total:</th>
                <td id="total">$0.00</td>
              </tr>
            </table>
            <div class="form-group">
              <label for="payment_Method">Payment Method:</label>
              <select class="form-control" onchange="show_help()" name="payment_Method" id="payment_Method">
                <option value="select">Select</option>
                <option value="cash">Cash</option>
                <option value="card">Card</option>
                <option value="something">Something else</option>
              </select>
            </div>
            <div class="form-inline">
              <button type="button" class="btn btn-primary" onclick="addItem()">Add Item</button>
              <button class="btn btn-primary pull-right" type = "submit" id = "addSaleSubmit">Create Sale</button>
            </div>
            <input id="count" name="count" class="sr-only" type="number"></input>
        </form>
      </div>
    </div>
  </body>
</html>
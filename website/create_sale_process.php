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

      	//TODO: 
      	//include("template.php");
        
      	echo "<div class=\"container custom custom-header\">";
      	
      	// Prevents basic sql injections
      	function secure_input($input) {
			$input = trim($input);
			$input = htmlspecialchars($input);
			return $input;
		}

		function validate(){
			$err_msg = "";
			$count = $_POST["count"];
			for ($i = 1; $i <= $count; $i++)
			{
				// Checks if stock item exists in database
				if (array_key_exists("KeyCode".$i, $_POST))
				{
					$to_test = $_POST["KeyCode".$i];
					$sql_table = "Stock";
					$query = "SELECT * FROM $sql_table WHERE KeyCode = '$to_test'";
					$result = mysqli_query($conn, $query);
					if (mysqli_num_rows($result) == 0)
					{
						$err_msg = $err_msg."One or more items had an invalid KeyCode.<br />";
					}
				}
				// Checks for bad quantity values
				$to_test = $_POST["Quantity".$i];
				if ($to_test < 1)
				{
					$err_msg = $err_msg."One or more items had a quantity of less than 1.";
				}
			}
			// checks payment method not select, needs more work
			$method = $_POST["payment_Method"];
			if ($method != "select"){
				$err_msg = $err_msg."Payment method not specified.<br />";
			}
		{
			return $err_msg;
		}

		$err_msg = validate();
		if ($err_msg != "")
		{
			echo "<h1>There was an error in the data you provided.</h1><br /><p class=\"lead\">The error(s) are listed below:<br /><br />".$err_msg."<p>";
		}
		else
		{
			if(!$conn)
			{
				echo "<h1>Database connection failure.</h1><br /><p class=\"lead\">Please contact your system administrator for support.<p>";
			}
			else
			{
				$success = true;
				$successCode = "";
				$sql_table="Sales";
				$result = mysqli_query($conn, $query);
				if (!$result)
				{
					$success = false;
					$successCode .= mysqli_error($conn);
				}
				// Sets up sale ID for transaction
				$query = "SELECT LAST_INSERT_ID()";
				$result = mysqli_query($conn, $query);
				if (!$result)
				{
					$success = false;
					$successCode .= "a";
				}
				$saleID = mysqli_fetch_assoc($result)["LAST_INSERT_ID()"];
				$saleTotal = 0;
				$count = $_POST["count"];
				for ($i = 1; $i <= $count; $i++)
				{
					if (array_key_exists("KeyCode".$i, $_POST))
					{
						$sql_table = "Stock";
						$keyCode = $_POST["KeyCode".$i];
						$method = $_POST["payment_Method"];
						$query = "SELECT Price FROM $sql_table WHERE KeyCode = '$keyCode'";
						$result = mysqli_query($conn, $query);
						if (!$result)
						{
							$success = false;
							$successCode .= "p";
						}
						$row = mysqli_fetch_assoc($result);
						$quantity = $_POST["Quantity".$i];
						$price = $row["Price"];
						$total = $price * $quantity;

						$sql_table = "SaleItems";
						$query = "SELECT Qty FROM $sql_table WHERE SaleID = '$saleID' AND KeyCode = '$keyCode'";
						$result = mysqli_query($conn, $query);
						if (!$result)
						{
							$success = false;
							$successCode .= "q";
						}
						if (mysqli_num_rows($result) == 0)
						{
							$query = "INSERT INTO $sql_table (SaleID, KeyCode, Qty, Total, payment_Method) VALUES ('$saleID', '$keyCode', '$quantity', '$total', '$payment_Method')";
						}
						else
						{
							$row = mysqli_fetch_assoc($result);
							$quantity = $quantity + $row["Qty"];
							$total = $price * $quantity;
							$query = "UPDATE $sql_table SET Qty = '$quantity', Total = '$total' WHERE KeyCode = '$keyCode' AND SaleID = '$saleID'";
						}
						$result = mysqli_query($conn, $query);
						if (!$result)
						{
							$success = false;
							$successCode .= "i";
						}
						$saleTotal = $saleTotal + $total;
					}
				}
				$sql_table = "Sales";
				$query = "UPDATE $sql_table SET SaleTotal = '$saleTotal' WHERE SaleID = '$saleID'";
				$result = mysqli_query($conn, $query);
				if (!$result)
				{
					$success = false;
					$successCode .= "u";
				}
				if (!$success)
				{
					echo "<h1>An error has occured.</h1><br /><p class=\"lead\">Please contact your system administrator. Success Code: $successCode<p>";
					echo "</div>";
				}
				else
				{
					/*
					TODO: print reciept? take back to sales page?
					*/
					echo "<h1>Sale complete </h1>";

			
					<?php
					// Frees up the memory, after using the result pointer
					mysqli_free_result($result);
				}
			}
		}
		mysqli_close($conn);
    ?>
  </body>
</html>

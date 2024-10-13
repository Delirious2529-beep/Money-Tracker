<?php

// Database connection details
$servername = "localhost";
$username = "root";
$password = "picklePlant88";
$dbname = "transactionTracker";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$transactionId = $_POST['transactionId'];

$sql = "delete from transactions where id=$transactionId";

if ($conn->query($sql) === TRUE) {
    echo "&check; Tansaction added.";
  } else {
    echo "Error: " . $sql . "<br>" . $conn->error;
  }
  
  $conn->close();
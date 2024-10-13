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

// Get the starting date from the form (assuming it's in YYYY-MM-DD format)
$startDate = $_POST['transactionDate'];

// Get the repeat frequency from the form
$frequency = $_POST['frequency']; // 'monthly' or '4weekly'
$amount = (float)$_POST['amount'];
$type = $_POST['transactionType'];
$description = $_POST['description'];


// SQL query to insert data
$sql = "INSERT INTO transactions (description, amount, type, start_date, frequency) 
        VALUES ('$description','$amount','$type','$startDate', '$frequency')";

if ($conn->query($sql) === TRUE) {
  echo "&check; Tansaction added.";
} else {
  echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
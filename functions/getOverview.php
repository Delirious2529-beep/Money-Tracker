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

// Get the start and end dates of the current pay cycle
$currentDate = date('Y-m-d');
$payCycleStart = date('Y-m-23', strtotime($currentDate . ' -1 month')); 
$payCycleEnd = date('Y-m-22', strtotime($currentDate));

if(isset($_COOKIE['StartingBalance'])) {
    $startingBalance = floatval($_COOKIE['StartingBalance']); // Convert cookie value to a float
  } else {
    $startingBalance = 0.00; // Default value if the cookie is not set
  }

// SQL query to fetch transactions within the pay cycle
$sql = "SELECT * FROM transactions 
        WHERE (
              (frequency = 'once' AND start_date BETWEEN '$payCycleStart' AND '$payCycleEnd') 
              OR 
              (frequency != 'once' AND start_date <= '$payCycleEnd') 
             )
        ORDER BY start_date"; 

$transactions = $conn->query($sql);

// Calculate income, expenses, and balance
$income = 0;
$expenses = 0;
$balance = $startingBalance;

while($transaction = $transactions->fetch_assoc()) {
    if ($transaction['type'] == 'income') {
        $income += number_format($transaction['amount'], 2);
    } else {
        $expenses += number_format($transaction['amount'], 2);
    }
}

$balance = $balance + ($income - $expenses);

// Display the results
echo date('D jS \of M',strtotime($payCycleStart)) . " to " . date('D jS \of M',strtotime($payCycleEnd)) .'|'.number_format($income, 2).'|'.number_format($expenses, 2).'|'.number_format($balance, 2);

// Close the connection
$conn = null;
?>
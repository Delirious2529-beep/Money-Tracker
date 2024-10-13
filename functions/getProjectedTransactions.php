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
$payCycleStart = date('Y-m-23', strtotime($currentDate)); 
$payCycleEnd = date('Y-m-22', strtotime($currentDate . ' +1 month'));

// Function to calculate occurrences within the pay cycle
function getOccurrencesWithinPayCycle($startDate, $frequency, $endDate, $payCycleStart, $payCycleEnd) {
    $occurrences = [];
    $currentDate = $startDate;
    $limit = 10;
    $count = 0;
  
    while ($endDate === NULL || $currentDate <= $endDate) {
        $count++;

        if($count >=  $limit) { break;}
      if ($currentDate >= $payCycleStart && $currentDate <= $payCycleEnd) {
        $occurrences[] = $currentDate;
      }
  
      if ($frequency == 'monthly') {
        $currentDate = date('Y-m-d', strtotime($currentDate . ' +1 month'));
      } else if ($frequency == '4weekly') {
        $currentDate = date('Y-m-d', strtotime($currentDate . ' +4 weeks'));
      } else {
         break; // For 'once' frequency, only check the start date
      }
    }
    return $occurrences;
  }

// SQL query to fetch transactions within the pay cycle
$sql = "SELECT t.id, t.start_date, t.frequency, t.end_date, t.type, t.description, t.amount
        FROM transactions t
        WHERE (
              (t.frequency = 'once' AND t.start_date BETWEEN '$payCycleStart' AND '$payCycleEnd') 
              OR 
              (t.frequency != 'once' AND t.start_date <= '$payCycleEnd') 
             )
        ORDER BY t.start_date, t.type ASC, t.description";  

$results = $conn->query($sql);

// Calculate income, expenses, and balance
$runningTotal = (float) str_replace(',','',$_GET['curBal']);
$timelineData = '<li class="balances"><h3>&pound;'.number_format($runningTotal, 2).'</h3></li>';
$groupedTransactions=[];
while($row = $results->fetch_assoc()) {
   $occurrences = getOccurrencesWithinPayCycle(
    $row['start_date'],
    $row['frequency'],
    $row['end_date'],
    $payCycleStart,
    $payCycleEnd
    );

    foreach ($occurrences as $occurrence) {
        $key = date('Y-m-d', strtotime($occurrence));
        if (!isset($groupedTransactions[$key])) {
            $groupedTransactions[$key] = [];
        }
        $groupedTransactions[$key][] = $row; // Store the entire transaction row
    }
}

foreach ($groupedTransactions as $date => $group) {
    $timelineData .= '<li>';
    $due = '';

    if(strtotime($date) == strtotime($currentDate)) {
      $due = 'Today';
    }elseif(strtotime($date) <= strtotime($currentDate.' +1 week') && strtotime($date) > strtotime($currentDate)) {
      $due = 'due this week';
    }

    if(!empty($due)) $due = ' - '.$due;
    $timelineData .= '<h3>' . date('D jS', strtotime($date)) .$due.'</h3>';
    
    foreach($group as $transaction) {
      //  $timelineData .= '<p class="' . ($transaction['type'] == 'income' ? 'income' : 'expenses') . '">';
      //  $timelineData .= '&pound;'.$transaction['amount'] . ' '. $transaction['description'].'</p>';
        
        if($transaction['type'] == 'income') {
          $runningTotal = $runningTotal + $transaction['amount'];
        }else{
          $runningTotal = $runningTotal - $transaction['amount'];
        }

       // $timelineData .= "<button class=\"delete-button\" data-transaction-id=\"" . $transaction['id'] . "\" aria-label=\"Delete Transaction\">&times;</button>"; // Add delete button

        $class = ($transaction['type'] == 'income') ? 'income' : 'expenses';
        $timelineData .=  '<div class="transaction-item '.$class.'">';
        $timelineData .=  '<p class="description">' . $transaction['description'].' <span class="frequency">'.$transaction['frequency'].'</span></p>'.'<p class="amount">&pound;' . $transaction['amount'].'</p>';
        $timelineData .=  '<button class="delete-button" data-transaction-id="' . $transaction['id'] . '" aria-label="Delete Transaction\"></button>'; // Add delete button
        $timelineData .=  '</div>';
    }

    if($runningTotal < 0) {
      $timelineData .= '<span class="runningTotal warning">&pound;'.number_format($runningTotal, 2).'</span>';
    }else{
      $timelineData .= '<span class="runningTotal">&pound;'.number_format($runningTotal, 2).'</span>';
    }

    $timelineData .= '</li>';
}

if($runningTotal < 0) {
  $timelineData .= '<li class="balances"><h3 id="closingBalance" class="warning">&pound;'.number_format($runningTotal, 2).'</h3></li>';
}else{
  $timelineData .= '<li class="balances"><h3 id="closingBalance">&pound;'.number_format($runningTotal, 2).'</h3></li>';
}

// Display the results
echo $timelineData;

// Close the connection
$conn = null;
?>
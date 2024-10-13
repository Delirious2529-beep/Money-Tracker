// Get all the buttons that open modals
const modalButtons = document.querySelectorAll('[data-modal-target]');
const closeModalButtons = document.querySelectorAll('.close');

// Add event listeners to each button
modalButtons.forEach(button => {
  button.addEventListener('click', () => {
    const modal = document.querySelector(button.dataset.modalTarget);
    openModal(modal);
  });
});

closeModalButtons.forEach(button => {
  button.addEventListener('click', () => {
    const modal = button.closest('.modal');
    closeModal(modal);
  });
});

function openModal(modal) {
  if (modal == null) return
  modal.style.display = 'block';
}

function closeModal(modal) {
  if (modal == null) return
  modal.style.display = 'none';
}

// Close modal when clicking outside the modal content
window.onclick = function(event) {
  if (event.target.classList.contains('modal')) 
 {
    event.target.style.display = "none";
  }
}

// Add Transaction
const form = document.getElementById('addTransaction');
const form2 = document.getElementById('setStartBalance');
const responseDiv = document.getElementById('response');

form2.addEventListener('submit', setStartingBalance);

form.addEventListener('submit', (event) => {
  event.preventDefault(); // Prevent default form submission

  const startDate = document.getElementById('transactionDate').value;
  const frequency = document.getElementById('repeatingOptions').options[document.getElementById('repeatingOptions').selectedIndex].value;
  const amount = document.getElementById('amount').value;
  // Get all radio buttons with the name "option"
  const radioButtons = document.querySelectorAll('input[name="transactionType"]');

  // Find the selected radio button
  let selectedValue;
  for (const radioButton of radioButtons) {
    if (radioButton.checked) {
      selectedValue = radioButton.value;
      break;
    }
  }

  const transactionType = selectedValue;
  const description = document.getElementById('description').value;

  // Create an AJAX request
  const xhr = new XMLHttpRequest();
  xhr.open("POST", "/functions/addTransaction.php", true);
  xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

  xhr.onreadystatechange = function() 
{
    if (xhr.readyState === 4 && xhr.status === 200) {
      console.log(xhr.responseText); // Display the response from the PHP script
    }
  };

  // Send the data to the PHP script
  xhr.send("transactionDate=" + startDate + "&frequency=" + frequency + "&amount=" + amount + "&transactionType=" + transactionType + "&description=" + description);

  getTransactions();
  getOverview();
  closeModal(form);
});

// Retrieve transactions
window.onload = function() {
  getOverview();
  getTransactions();
};

function getOverview() {
  // Create an AJAX request
  const xhr = new XMLHttpRequest();
  xhr.open("GET", "/functions/getOverview.php", true);

  xhr.onreadystatechange = function() 
{
    if (xhr.readyState === 4 && xhr.status === 200) {
      let dashboardComponents = xhr.responseText.split('|');

      document.getElementById('payCycle').innerHTML = dashboardComponents[0]
      document.getElementById('income').innerHTML = '<p>&pound;'+dashboardComponents[1]+'</p>';
      document.getElementById('expenses').innerHTML = '<p>&pound;'+dashboardComponents[2]+'</p>';
      document.getElementById('difference').innerHTML = '<p>&pound;'+dashboardComponents[3]+'</p>';
      document.getElementById('difference').className ='';
      if(dashboardComponents[3] < 0) document.getElementById('difference').classList.add('warning');
    }
  };

  xhr.send();
}

function getProjection() {
  // Create an AJAX request
  const xhr = new XMLHttpRequest();
  let curBal = document.getElementById('closingBalance').innerText.substring(1);
  xhr.open("GET", "/functions/getProjection.php?curBal="+curBal+0, true);

  xhr.onreadystatechange = function() 
{
    if (xhr.readyState === 4 && xhr.status === 200) {
      let dashboardComponents = xhr.responseText.split('|');

      document.getElementById('projectedPayCycle').innerHTML = dashboardComponents[0]
      document.getElementById('projectedIncome').innerHTML = '<p>&pound;'+dashboardComponents[1]+'</p>';
      document.getElementById('projectedExpenses').innerHTML = '<p>&pound;'+dashboardComponents[2]+'</p>';
      document.getElementById('projectedDifference').innerHTML = '<p>&pound;'+dashboardComponents[3]+'</p>';

      getProjectedTransactions();
    }
  };

  xhr.send();
}

// ... (other JavaScript code) ...

function getTransactions() {
  // Create an AJAX request
  const xhr = new XMLHttpRequest();
  xhr.open("GET", "/functions/getTransactions.php", true);

  xhr.onreadystatechange = function() {
    if (xhr.readyState === 4 && xhr.status === 200) {
      document.getElementById('timeline-data').innerHTML = xhr.responseText;

      // Add event listeners to delete buttons after the content is loaded
      const deleteButtons = document.querySelectorAll('.delete-button');
      deleteButtons.forEach(button => {
        button.addEventListener('click', () => {
          const transactionId = button.dataset.transactionId;
          deleteTransaction(transactionId);
        });
      });

      getProjection();
    }
  };

  xhr.send();
}

function getProjectedTransactions() {
  // Create an AJAX request
  const xhr = new XMLHttpRequest();
  let curBal = document.getElementById('closingBalance').innerText.substring(1);
  xhr.open("GET", "/functions/getProjectedTransactions.php?curBal="+curBal, true);

  xhr.onreadystatechange = function() {
    if (xhr.readyState === 4 && xhr.status === 200) {
      document.getElementById('projectedTimeline-data').innerHTML = xhr.responseText;

      // Add event listeners to delete buttons after the content is loaded
      const deleteButtons = document.querySelectorAll('.delete-button');
      deleteButtons.forEach(button => {
        button.addEventListener('click', () => {
          const transactionId = button.dataset.transactionId;
          deleteTransaction(transactionId);
        });
      });
    }
  };

  xhr.send();
}

function deleteTransaction(transactionId) {
  // Create an AJAX request to delete the transaction
  const xhr = new XMLHttpRequest();
  xhr.open("POST", "/functions/deleteTransaction.php", true);
  xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

  xhr.onreadystatechange = function() {
    if (xhr.readyState === 4 && xhr.status === 200) {
      // Handle the response (e.g., display a success message)
      console.log(xhr.responseText);

      // Refresh the transactions
      getTransactions();
      getOverview();
    }
  };

  // Send the transaction ID to the PHP script
  xhr.send("transactionId=" + transactionId);
}

function setStartingBalance(event) {
  event.preventDefault();
    // Set the starting balance (replace with your actual value or get it from user input)
  const startingBalance = document.getElementById('startBalance').value;
  // Create a cookie named "StartingBalance" with the value and an expiration date (e.g., 30 days)
  const expirationDate = new Date();
  expirationDate.setTime(expirationDate.getTime() + (30 * 24 * 60 * 60 * 1000)); // 30 days in milliseconds
  const expires = "expires=" + expirationDate.toUTCString();
  document.cookie = "StartingBalance=" + startingBalance + ";" + expires + ";path=/";

  getOverview();
  getTransactions();
  closeModal(form2);
}

const toggleTransactions = document.getElementById('toggleTransactionList');
const toggleProjectedTransactions = document.getElementById('toggleProjectedTransactionList');
const toggleTransactionLabel = document.getElementById('toggleTransactionListButton');
const toggleProjectedTransactionLabel = document.getElementById('toggleProjectedTransactionListButton');
const transactionsList = document.getElementsByTagName('main')[0];
const projectedTransactionsList = document.getElementsByTagName('footer')[0];
const timelineData = document.getElementById('timeline-data');
const projectedTimelineData = document.getElementById('projectedTimeline-data');

toggleTransactions.addEventListener('change', function() {
  if(transactionsList.className == '') {
    transactionsList.classList.add('expanded');
    timelineData.classList.add('expanded');
    toggleTransactionLabel.innerText = 'Hide Transactions';
    projectedTransactionsList.classList.remove('expanded');
  }else{
    transactionsList.classList.remove('expanded');
    timelineData.classList.remove('expanded');
    toggleTransactionLabel.innerText = 'Show Transactions';
  }
});

toggleProjectedTransactions.addEventListener('change', function() {
  if(projectedTransactionsList.className == '') {
    projectedTransactionsList.classList.add('expanded');
    projectedTimelineData.classList.add('expanded');
    toggleProjectedTransactionLabel.innerText = 'Hide Transactions';
    transactionsList.classList.remove('expanded');
  }else{
    projectedTransactionsList.classList.remove('expanded');
    projectedTimelineData.classList.remove('expanded');
    toggleProjectedTransactionLabel.innerText = 'Show Transactions';
  }
});
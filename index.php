<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, intial-scale=1">
    <title>Money Tracker</title>

    <link rel="stylesheet" type="text/css" href="/css/base.css">
</head>

<body>
    <header>
        <h1>Money Tracker</h1>
        <nav>
            <button data-modal-target="#addTransaction" aria-label="Add Transaction">&plus;</button>
            <button data-modal-target="#setStartBalance" aria-label="Adjust Starting Balance">Adjust Starting Balance</button>
        </nav>
    </header>

    <main>
        <h2 id="payCycle" aria-live="polite"></h2>
        <div id="dashboard">
            <div id="income" class="data-circle">
                <strong class="income">&pound;0.00</strong>
            </div>
            <div id="expenses" class="data-circle">
                <strong class="expense">&pound;0.00</strong>
            </div>
            <div id="difference">
                <p>&pound;0.00</p>
            </div>
        </div>

        <div id="timeline">
            <label for="toggleTransactionList" id="toggleTransactionListButton">Show Transactions</label>
            <input type="checkbox" id="toggleTransactionList">

            <ul id="timeline-data" aria-live="polite">
                <li>No Transactions</li>
            </ul>
        </div>
    </main>

    <div id="response" aria-live="polite"></div>

    <div id="addTransaction" class="modal">
        <div class="modal-content">
            <span class="close" aria-label="Close">&times;</span>
            <form id="addTransactionForm">
                <p><label for="transactionDate">Date:</label> <input type="date" id="transactionDate"></p>
                <p>
                    <label for="repeatingOptions">Repeats:</label>
                    <select id="repeatingOptions" aria-label="Repeating Options">
                        <option value="once">Does not repeat</option>
                        <option value="monthly">Monthly</option>
                        <option value="4weeks">Every 4 Weeks</option>
                    </select>
                </p>
                <p><label for="amount">Amount: &pound;</label><input type="number" id="amount" step="any" value="0.00"></p>
                <p>
                    <label class="radio-selector typeIncome">
                        <input type="radio" name="transactionType" value="income"> Income
                    </label>
                    <label class="radio-selector typeExpense">
                        <input type="radio" name="transactionType" value="expense"> Expense
                    </label>
                </p>
                <p><input id="description" type="text" placeholder="Name of Transaction"></p>
                <p><input type="submit" value="Add Transaction"></p>
            </form>
        </div>
    </div>

    <div id="setStartBalance" class="modal">
        <div class="modal-content">
			<span class="close" aria-label="Close">&times;</span>
            <form>
                <p><label for="startBalance">&pound;</label><input type="number" id="startBalance" step="any" value="0.00"></p>
                <p><input type="submit" value="Set Start Balance"></p>
            </form>
        </div>
    </div>

    <footer>
        <div id="projections">
            <h3 id="projectedPayCycle"></h3>
            <div id="projectedIncome" class="data-circle">
                <strong class="income">&pound;0.00</strong>
            </div>
            <div id="projectedExpenses" class="data-circle">
                <strong class="expenses">&pound;0.00</strong>
            </div>
            <div id="projectedDifference">
                <p>&pound;0.00</p>
            </div>
        </div>

        <div id="projectedTimeline">
            <label for="toggleProjectedTransactionList" id="toggleProjectedTransactionListButton">Show Transactions</label>
            <input type="checkbox" id="toggleProjectedTransactionList">

            <ul id="projectedTimeline-data" aria-live="polite">
                
            </ul>
        </div>
    </footer>
</body>
<script type="text/javascript" src="/js/base.js"></script>
</html>
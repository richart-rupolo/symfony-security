const API_BASE = '/api/harmony';

async function makeRequest(url, options = {}) {
    try {
        const response = await fetch(url, {
            headers: {
                'Content-Type': 'application/json',
                ...options.headers
            },
            ...options
        });
        const data = await response.json();
        return { success: response.ok, data, status: response.status };
    } catch (error) {
        return { success: false, data: { message: error.message }, status: 500 };
    }
}

function showResult(elementId, result) {
    const element = document.getElementById(elementId);
    element.style.display = 'block';
    element.className = `result ${result.success ? 'success' : 'error'}`;
    element.textContent = JSON.stringify(result.data, null, 2);
}

// Create Account
document.addEventListener("DOMContentLoaded", () => {
    const accountForm = document.getElementById('createAccountForm');

    if (accountForm) {
        accountForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = {
                customerName: document.getElementById('customerName').value,
                accountNumber: document.getElementById('accountNumber').value,
                balance: parseFloat(document.getElementById('balance').value),
                ssn: document.getElementById('ssn').value,
                email: document.getElementById('email').value
            };

            const result = await makeRequest(`${API_BASE}/accounts`, {
                method: 'POST',
                body: JSON.stringify(formData)
            });

            showResult('createAccountResult', result);
        });
    }

    // Create Transaction
    const transactionForm = document.getElementById('createTransactionForm');

    if (transactionForm) {
        transactionForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = {
                accountNumber: document.getElementById('txAccountNumber').value,
                amount: parseFloat(document.getElementById('amount').value),
                transactionType: document.getElementById('transactionType').value,
                description: document.getElementById('description').value,
                cardNumber: document.getElementById('cardNumber').value,
                cvv: document.getElementById('cvv').value,
                expiryDate: document.getElementById('expiryDate').value,
                merchantName: document.getElementById('merchantName').value
            };

            const result = await makeRequest(`${API_BASE}/transactions`, {
                method: 'POST',
                body: JSON.stringify(formData)
            });

            showResult('createTransactionResult', result);
        });
    }
});

// Query Functions
async function findAccountByNumber() {
    const accountNumber = document.getElementById('queryAccountNumber').value;
    const result = await makeRequest(`${API_BASE}/accounts/${accountNumber}`);
    showResult('accountQueryResult', result);
}

async function findAccountBySsn() {
    const ssn = document.getElementById('querySsn').value;
    const result = await makeRequest(`${API_BASE}/accounts/ssn/${ssn}`);
    showResult('accountQueryResult', result);
}

async function findAccountsByBalanceRange() {
    const min = document.getElementById('minBalance').value;
    const max = document.getElementById('maxBalance').value;
    const result = await makeRequest(`${API_BASE}/accounts/balance-range?min=${min}&max=${max}`);
    showResult('accountQueryResult', result);
}

async function getAllAccounts() {
    const result = await makeRequest(`${API_BASE}/accounts`);
    showResult('accountQueryResult', result);
}

async function findTransactionsByAccountNumber() {
    const accountNumber = document.getElementById('queryTxAccountNumber').value;
    const result = await makeRequest(`${API_BASE}/transactions/account/${accountNumber}`);
    showResult('transactionQueryResult', result);
}

async function findTransactionsByAmountRange() {
    const min = document.getElementById('minAmount').value;
    const max = document.getElementById('maxAmount').value;
    const result = await makeRequest(`${API_BASE}/transactions/amount-range?min=${min}&max=${max}`);
    showResult('transactionQueryResult', result);
}

async function getAllTransactions() {
    const result = await makeRequest(`${API_BASE}/transactions`);
    showResult('transactionQueryResult', result);
}

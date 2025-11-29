<?php

namespace App\Service;

use App\Document\Account;
use App\Document\Transaction;
use Doctrine\ODM\MongoDB\DocumentManager;

class FinancialService
{
    public function __construct(
        private readonly DocumentManager $dm
    ) {}

    // ============================
    //  ACCOUNT HELPERS
    // ============================

    private function accountToArray(Account $a): array
    {
        return [
            'id'            => $a->getId(),
            'customerName'  => $a->getCustomerName(),
            'accountNumber' => $a->getAccountNumber(),
            'balance'       => $a->getBalance(),
            'ssn'           => $a->getSsn(),
            'email'         => $a->getEmail(),
            'createdAt'     => $a->getCreatedAt()->format(DATE_ATOM),
        ];
    }

    // ============================
    //  TRANSACTION HELPERS
    // ============================

    private function transactionToArray(Transaction $t): array
    {
        return [
            'id'              => $t->getId(),
            'accountNumber'   => $t->getAccountNumber(),
            'amount'          => $t->getAmount(),
            'transactionType' => $t->getTransactionType(),
            'description'     => $t->getDescription(),
            'cardNumber'      => $t->getCardNumber(),
            'cvv'             => $t->getCvv(),
            'expiryDate'      => $t->getExpiryDate(),
            'merchantName'    => $t->getMerchantName(),
            'transactionDate' => $t->getTransactionDate()->format(DATE_ATOM),
            'status'          => $t->getStatus(),
        ];
    }

    // ============================
    //  CREATE ACCOUNT
    // ============================

    public function createAccount(
        string $customerName,
        string $accountNumber,
        float $balance,
        string $ssn,
        string $email
    ): Account {

        $account = new Account(
            customerName: $customerName,
            accountNumber: $accountNumber,
            balance: $balance,
            ssn: $ssn,
            email: $email
        );

        $this->dm->persist($account);
        $this->dm->flush();

        return $account;
    }

    // ============================
    //  QUERIES (RETORNAM ARRAYS!)
    // ============================

    public function getAllAccounts(): array
    {
        return array_map(
            fn(Account $a) => $this->accountToArray($a),
            $this->dm->getRepository(Account::class)->findAll()
        );
    }

    public function findAccountByNumber(string $accountNumber): array
    {
        $acc = $this->dm->getRepository(Account::class)
            ->findOneBy(['accountNumber' => $accountNumber]);

        return $acc ? $this->accountToArray($acc) : [];
    }

    public function findAccountBySsn(string $ssn): array
    {
        $acc = $this->dm->getRepository(Account::class)
            ->findOneBy(['ssn' => $ssn]);

        return $acc ? $this->accountToArray($acc) : [];
    }

    public function findAccountsByBalanceRange(float $minBalance, float $maxBalance): array
    {
        $results = $this->dm->getRepository(Account::class)
            ->createQueryBuilder()
            ->field('balance')->gte($minBalance)
            ->field('balance')->lte($maxBalance)
            ->getQuery()
            ->execute()
            ->toArray();

        return array_map(
            fn(Account $a) => $this->accountToArray($a),
            $results
        );
    }

    // ============================
    //  TRANSACTIONS (CREATE)
    // ============================

    public function createTransaction(
        string $accountNumber,
        float $amount,
        string $transactionType,
        string $description,
        ?string $cardNumber,
        ?string $cvv,
        ?string $expiryDate,
        string $merchantName
    ): Transaction {

        $transaction = new Transaction(
            accountNumber: $accountNumber,
            amount: $amount,
            transactionType: $transactionType,
            description: $description,
            cardNumber: $cardNumber,
            cvv: $cvv,
            expiryDate: $expiryDate,
            merchantName: $merchantName
        );

        $this->dm->persist($transaction);
        $this->dm->flush();

        return $transaction;
    }

    // ============================
    //  TRANSACTIONS (QUERIES)
    // ============================

    public function getAllTransactions(): array
    {
        return array_map(
            fn(Transaction $t) => $this->transactionToArray($t),
            $this->dm->getRepository(Transaction::class)->findAll()
        );
    }

    public function findTransactionsByAccountNumber(string $accountNumber): array
    {
        $results = $this->dm->getRepository(Transaction::class)
            ->findBy(['accountNumber' => $accountNumber]);

        return array_map(
            fn(Transaction $t) => $this->transactionToArray($t),
            $results
        );
    }

    public function findTransactionsByAmountRange(float $minAmount, float $maxAmount): array
    {
        $results = $this->dm->getRepository(Transaction::class)
            ->createQueryBuilder()
            ->field('amount')->gte($minAmount)
            ->field('amount')->lte($maxAmount)
            ->getQuery()
            ->execute()
            ->toArray();

        return array_map(
            fn(Transaction $t) => $this->transactionToArray($t),
            $results
        );
    }
}

<?php

namespace App\Controller;

use App\DTO\CreateAccountDTO;
use App\DTO\CreateTransactionDTO;
use App\Service\FinancialService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class HarmonyController extends AbstractController
{
    public function __construct(
        private readonly FinancialService $financialService
    ) {}

    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function home()
    {
        return $this->render('harmony/index.html.twig');
    }

    // ============================
    // ACCOUNTS
    // ============================

    #[Route('/api/harmony/accounts', methods: ['POST'])]
    public function createAccount(Request $request): JsonResponse
    {
        $payload = json_decode($request->getContent(), true);
        $dto = CreateAccountDTO::fromArray($payload);

        $account = $this->financialService->createAccount(
            $dto->customerName,
            $dto->accountNumber,
            $dto->balance,
            $dto->ssn,
            $dto->email
        );

        return $this->json($account->toArray());
    }

    #[Route('/api/harmony/accounts', methods: ['GET'])]
    public function getAllAccounts(): JsonResponse
    {
        return $this->json($this->financialService->getAllAccounts());
    }

    #[Route('/api/harmony/accounts/{accountNumber}', methods: ['GET'])]
    public function getByAccountNumber(string $accountNumber): JsonResponse
    {
        return $this->json(
            $this->financialService->findAccountByNumber($accountNumber)
        );
    }

    #[Route('/api/harmony/accounts/ssn/{ssn}', methods: ['GET'])]
    public function getBySsn(string $ssn): JsonResponse
    {
        return $this->json(
            $this->financialService->findAccountBySsn($ssn)
        );
    }

    #[Route('/api/harmony/accounts/balance-range', methods: ['GET'])]
    public function getByBalanceRange(Request $request): JsonResponse
    {
        return $this->json(
            $this->financialService->findAccountsByBalanceRange(
                (float) $request->query->get('min'),
                (float) $request->query->get('max')
            )
        );
    }

    // ============================
    // TRANSACTIONS
    // ============================

    #[Route('/api/harmony/transactions', methods: ['POST'])]
    public function createTransaction(Request $request): JsonResponse
    {
        $payload = json_decode($request->getContent(), true);
        $dto = CreateTransactionDTO::fromArray($payload);

        $transaction = $this->financialService->createTransaction(
            $dto->accountNumber,
            $dto->amount,
            $dto->transactionType,
            $dto->description,
            $dto->cardNumber,
            $dto->cvv,
            $dto->expiryDate,
            $dto->merchantName
        );

        return $this->json($transaction->toArray());
    }

    #[Route('/api/harmony/transactions', methods: ['GET'])]
    public function getAllTransactions(): JsonResponse
    {
        return $this->json($this->financialService->getAllTransactions());
    }

    #[Route('/api/harmony/transactions/account/{accountNumber}', methods: ['GET'])]
    public function getTransactionsByAccount(string $accountNumber): JsonResponse
    {
        return $this->json(
            $this->financialService->findTransactionsByAccountNumber($accountNumber)
        );
    }

    #[Route('/api/harmony/transactions/amount-range', methods: ['GET'])]
    public function getTransactionsByAmountRange(Request $request): JsonResponse
    {
        return $this->json(
            $this->financialService->findTransactionsByAmountRange(
                (float) $request->query->get('min'),
                (float) $request->query->get('max')
            )
        );
    }
}

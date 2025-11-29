<?php

namespace App\DTO;

class CreateTransactionDTO
{
    public function __construct(
        public string $accountNumber,
        public float $amount,
        public string $transactionType,
        public string $description,
        public ?string $cardNumber,
        public ?string $cvv,
        public ?string $expiryDate,
        public string $merchantName
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            $data['accountNumber'],
            (float) $data['amount'],
            $data['transactionType'],
            $data['description'],
            $data['cardNumber'] ?? null,
            $data['cvv'] ?? null,
            $data['expiryDate'] ?? null,
            $data['merchantName']
        );
    }

    public function toArray(): array
    {
        return [
            'accountNumber'  => $this->accountNumber,
            'amount'         => $this->amount,
            'transactionType'=> $this->transactionType,
            'description'    => $this->description,
            'cardNumber'     => $this->cardNumber,
            'cvv'            => $this->cvv,
            'expiryDate'     => $this->expiryDate,
            'merchantName'   => $this->merchantName,
        ];
    }
}

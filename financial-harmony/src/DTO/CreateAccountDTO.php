<?php

namespace App\DTO;

class CreateAccountDTO
{
    public function __construct(
        public string $customerName,
        public string $accountNumber,
        public float $balance,
        public string $ssn,
        public string $email
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            $data['customerName'],
            $data['accountNumber'],
            (float) $data['balance'],
            $data['ssn'],
            $data['email']
        );
    }

    public function toArray(): array
    {
        return [
            'customerName'  => $this->customerName,
            'accountNumber' => $this->accountNumber,
            'balance'       => $this->balance,
            'ssn'           => $this->ssn,
            'email'         => $this->email,
        ];
    }
}

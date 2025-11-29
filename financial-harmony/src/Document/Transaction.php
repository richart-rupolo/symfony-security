<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Encrypt;
use Doctrine\ODM\MongoDB\Mapping\Annotations\EncryptQuery;
use Symfony\Component\Validator\Constraints as Assert;

#[ODM\Document(collection: 'transactions')]
class Transaction
{
    #[ODM\Id]
    private ?string $id = null;

    public function __construct(
        #[ODM\Field(type: 'string')]
        #[Encrypt(queryType: EncryptQuery::Equality)]
        private string $accountNumber,

        #[Assert\NotNull]
        #[Assert\Type('float')]
        #[Assert\GreaterThanOrEqual(0)]
        #[Assert\LessThanOrEqual(1000000)]
        #[ODM\Field(type: 'float')]
        #[Encrypt]
        private float $amount,

        #[ODM\Field(type: 'string')]
        private string $transactionType,

        #[ODM\Field(type: 'string')]
        private string $description,

        #[ODM\Field(type: 'string')]
        #[Encrypt]
        private ?string $cardNumber,

        #[ODM\Field(type: 'string')]
        #[Encrypt]
        private ?string $cvv,

        #[ODM\Field(type: 'string')]
        #[Encrypt]
        private ?string $expiryDate,

        #[ODM\Field(type: 'string')]
        private string $merchantName,

        #[ODM\Field(type: 'date')]
        private readonly \DateTime $transactionDate = new \DateTime(),

        #[ODM\Field(type: 'string')]
        private string $status = 'pending'
    ) {}

    // ============================
    //        GETTERS
    // ============================

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getAccountNumber(): string
    {
        return $this->accountNumber;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getTransactionType(): string
    {
        return $this->transactionType;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getCardNumber(): ?string
    {
        return $this->cardNumber;
    }

    public function getCvv(): ?string
    {
        return $this->cvv;
    }

    public function getExpiryDate(): ?string
    {
        return $this->expiryDate;
    }

    public function getMerchantName(): string
    {
        return $this->merchantName;
    }

    public function getTransactionDate(): \DateTime
    {
        return $this->transactionDate;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    // ============================
    //  Business Methods
    // ============================

    public function markCompleted(): void
    {
        $this->status = 'completed';
    }

    public function markFailed(): void
    {
        $this->status = 'failed';
    }

    // ============================
    //  Array Serialization
    // ============================

    public function toArray(): array
    {
        return [
            'id'              => $this->id,
            'accountNumber'   => $this->accountNumber,
            'amount'          => $this->amount,
            'transactionType' => $this->transactionType,
            'description'     => $this->description,
            'cardNumber'      => $this->cardNumber,
            'cvv'             => $this->cvv,
            'expiryDate'      => $this->expiryDate,
            'merchantName'    => $this->merchantName,
            'transactionDate' => $this->transactionDate->format(DATE_ATOM),
            'status'          => $this->status,
        ];
    }
}

<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Encrypt;
use Doctrine\ODM\MongoDB\Mapping\Annotations\EncryptQuery;
use Symfony\Component\Validator\Constraints as Assert;

#[ODM\Document(collection: 'accounts')]
class Account
{
    #[ODM\Id]
    private ?string $id = null;

    public function __construct(
        #[ODM\Field(type: 'string')]
        private string $customerName,

        #[ODM\Field(type: 'string')]
        #[Encrypt(queryType: EncryptQuery::Equality)]
        private string $accountNumber,

        #[Assert\NotNull]
        #[Assert\Type('float')]
        #[Assert\GreaterThanOrEqual(0)]
        #[Assert\LessThanOrEqual(1000000)]
        #[ODM\Field(type: 'float')]
        #[Encrypt]
        private float $balance,

        #[ODM\Field(type: 'string')]
        #[Encrypt(queryType: EncryptQuery::Equality)]
        private string $ssn,

        #[ODM\Field(type: 'string')]
        private string $email,

        #[ODM\Field(type: 'date')]
        private \DateTime $createdAt = new \DateTime()
    ) {}

    // ============================
    //        GETTERS
    // ============================

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getCustomerName(): string
    {
        return $this->customerName;
    }

    public function getAccountNumber(): string
    {
        return $this->accountNumber;
    }

    public function getBalance(): float
    {
        return $this->balance;
    }

    public function getSsn(): string
    {
        return $this->ssn;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    // ============================
    //        ARRAY OUTPUT
    // ============================

    public function toArray(): array
    {
        return [
            'id'            => $this->id,
            'customerName'  => $this->customerName,
            'accountNumber' => $this->accountNumber,
            'balance'       => $this->balance,
            'ssn'           => $this->ssn,
            'email'         => $this->email,
            'createdAt'     => $this->createdAt->format(DATE_ATOM),
        ];
    }
}

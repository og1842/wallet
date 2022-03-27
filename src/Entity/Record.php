<?php declare(strict_types=1);

namespace App\Entity;

use App\Repository\RecordRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RecordRepository::class)]
class Record
{
    private const TYPE_CREDIT = 'Credit';
    private const TYPE_DEBIT = 'Debit';

    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 32)]
    private string $id;

    #[ORM\Column(type: 'string', length: 190)]
    private string $name;

    #[ORM\Column(type: 'bigint', options: ['unsigned' => true])]
    private int $amount;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    #[ORM\ManyToOne(targetEntity: Wallet::class)]
    #[ORM\JoinColumn(name: 'from_wallet_id', onDelete: 'CASCADE')]
    private ?Wallet $fromWallet;

    #[ORM\ManyToOne(targetEntity: Wallet::class)]
    #[ORM\JoinColumn(name: 'to_wallet_id', nullable: false, onDelete: 'CASCADE')]
    private ?Wallet $toWallet;

    #[ORM\Column(type: 'string', length: 32)]
    private ?string $fromWalletId;

    #[ORM\Column(type: 'string', length: 32)]
    private string $toWalletId;

    public function __construct(string $id = null)
    {
        $this->id = $id ?? IdGenerator::generate();
        $this->fromWalletId = null;
        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getFromWallet(): ?Wallet
    {
        return $this->fromWallet;
    }

    public function setFromWallet(?Wallet $fromWallet): self
    {
        $this->fromWallet = $fromWallet;

        return $this;
    }

    public function getToWallet(): ?Wallet
    {
        return $this->toWallet;
    }

    public function setToWallet(?Wallet $toWallet): self
    {
        $this->toWallet = $toWallet;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFromWalletId(): ?string
    {
        return $this->fromWalletId;
    }

    /**
     * @param string|null $fromWalletId
     */
    public function setFromWalletId(?string $fromWalletId): void
    {
        $this->fromWalletId = $fromWalletId;
    }

    /**
     * @return string
     */
    public function getToWalletId(): string
    {
        return $this->toWalletId;
    }

    /**
     * @param string $toWalletId
     */
    public function setToWalletId(string $toWalletId): void
    {
        $this->toWalletId = $toWalletId;
    }

    /**
     * Get record type
     *
     * @param string $walletId
     *
     * @return string
     */
    public function getType(string $walletId): string
    {
        if ($this->isCreditType($walletId)) {
            return self::TYPE_CREDIT;
        }

        return self::TYPE_DEBIT;
    }

    /**
     * @param string $walletId
     *
     * @return bool
     */
    public function isCreditType(string $walletId): bool
    {
        return $this->toWalletId === $walletId;
    }

    /**
     * @param string $walletId
     *
     * @return bool
     */
    public function isDebitType(string $walletId): bool
    {
        return $this->fromWalletId === $walletId;
    }
}
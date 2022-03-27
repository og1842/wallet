<?php declare(strict_types=1);

namespace App\Entity;

use App\Repository\WalletRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WalletRepository::class)]
class Wallet
{
    private const WALLET_TYPE_CREDIT_CARD = 'credit_card';
    private const WALLET_TYPE_CASH = 'cash';

    public const WALLET_TYPES = [self::WALLET_TYPE_CREDIT_CARD, self::WALLET_TYPE_CASH];

    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 32)]
    private string $id;

    #[ORM\Column(type: 'string', length: 190)]
    private string $name;

    #[ORM\Column(type: 'string', length: 50)]
    private string $walletType;

    #[ORM\Column(type: 'bigint', options: ['unsigned' => true])]
    private int $balance;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'wallets')]
    #[ORM\JoinColumn(name: 'user_id', nullable: false, onDelete: 'CASCADE')]
    private ?User $user = null;

    #[ORM\Column(type: 'integer')]
    private int $userId;

    public function __construct(string $id = null)
    {
        $this->id = $id ?? IdGenerator::generate();
        $this->balance = 0;
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

    public function getWalletType(): string
    {
        return $this->walletType;
    }

    public function setWalletType(string $walletType): self
    {
        $this->walletType = $walletType;

        return $this;
    }

    public function getBalance(): int
    {
        return $this->balance;
    }

    public function setBalance(int $balance): self
    {
        $this->balance = $balance;

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

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): self
    {
        $this->userId = $userId;

        return $this;
    }
}
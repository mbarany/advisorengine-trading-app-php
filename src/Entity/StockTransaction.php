<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\StockTransactionRepository")
 * @ORM\Table(name="`stock_transaction`")
 */
class StockTransaction implements \JsonSerializable
{
    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="guid")
     */
    private $id;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(nullable=false)
     *
     * @Assert\NotBlank()
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=10)
     *
     * @Assert\NotBlank()
     */
    private $symbol;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=10)
     *
     * @Assert\Choice(choices={"buy", "sell"})
     */
    private $type;

    /**
     * @var float
     *
     * @ORM\Column(type="decimal", precision=7, scale=2)
     *
     * @Assert\NotBlank()
     */
    private $amount;

    /**
     * @var float
     *
     * @ORM\Column(type="decimal", precision=7, scale=2)
     *
     * @Assert\NotBlank()
     */
    private $price;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     *
     * @return StockTransaction
     */
    public function setUser(User $user): StockTransaction
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return string
     */
    public function getSymbol(): string
    {
        return $this->symbol;
    }

    /**
     * @param string $symbol
     *
     * @return StockTransaction
     */
    public function setSymbol(string $symbol): StockTransaction
    {
        $this->symbol = $symbol;
        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return StockTransaction
     */
    public function setType(string $type): StockTransaction
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     *
     * @return StockTransaction
     */
    public function setAmount(float $amount): StockTransaction
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @param float $price
     *
     * @return StockTransaction
     */
    public function setPrice(float $price): StockTransaction
    {
        $this->price = $price;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     *
     * @return StockTransaction
     */
    public function setCreatedAt(\DateTime $createdAt): StockTransaction
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'user_id' => $this->getUser()->getId(),
            'symbol' => $this->getSymbol(),
            'amount' => $this->getAmount(),
            'price' => $this->getPrice(),
            'created_at' => $this->getCreatedAt()->format(\DateTime::ATOM),
        ];
    }
}

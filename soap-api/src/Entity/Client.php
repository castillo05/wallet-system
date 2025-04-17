<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClientRepository::class)]
class Client
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $document = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToOne(mappedBy: 'client', cascade: ['persist', 'remove'])]
    private ?Wallet $wallet = null;

    #[ORM\OneToMany(mappedBy: 'client', targetEntity: Transaction::class, orphanRemoval: true)]
    private Collection $transactions;

    #[ORM\OneToMany(mappedBy: 'client', targetEntity: PaymentSession::class, orphanRemoval: true)]
    private Collection $paymentSessions;

    public function __construct()
    {
        $this->transactions = new ArrayCollection();
        $this->paymentSessions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDocument(): ?string
    {
        return $this->document;
    }

    public function setDocument(string $document): static
    {
        $this->document = $document;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): static 
    {
        $this->phone = $phone;

        return $this;
    }

    public function getWallet(): ?Wallet
    {
        return $this->wallet;
    }

    public function setWallet(Wallet $wallet): static
    {
        // set the owning side of the relation if necessary
        if ($wallet->getClient() !== $this) {
            $wallet->setClient($this);
        }

        $this->wallet = $wallet;

        return $this;
    }

    /**
     * @return Collection<int, Transaction>
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    public function addTransaction(Transaction $transaction): static
    {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions->add($transaction);
            $transaction->setClient($this);
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction): static
    {
        if ($this->transactions->removeElement($transaction)) {
            // set the owning side to null (unless already changed)
            if ($transaction->getClient() === $this) {
                $transaction->setClient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, PaymentSession>
     */
    public function getPaymentSessions(): Collection
    {
        return $this->paymentSessions;
    }

    public function addPaymentSession(PaymentSession $paymentSession): static
    {
        if (!$this->paymentSessions->contains($paymentSession)) {
            $this->paymentSessions->add($paymentSession);
            $paymentSession->setClient($this);
        }

        return $this;
    }

    public function removePaymentSession(PaymentSession $paymentSession): static
    {
        if ($this->paymentSessions->removeElement($paymentSession)) {
            // set the owning side to null (unless already changed)
            if ($paymentSession->getClient() === $this) {
                $paymentSession->setClient(null);
            }
        }

        return $this;
    }
}

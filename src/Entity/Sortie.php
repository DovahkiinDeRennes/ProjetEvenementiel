<?php

namespace App\Entity;

use App\Repository\SortieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SortieRepository::class)]
class Sortie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateHeureDebut = null;

    #[ORM\Column]
    private ?int $duree = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateLimiteInscription = null;

    #[ORM\Column(length: 255)]
    private ?string $nbInscriptionMax = null;

    #[ORM\ManyToOne(inversedBy: 'sorties')]
    private ?Etat $etatId = null;

    #[ORM\ManyToOne(inversedBy: 'sorties')]
    private ?Lieu $lieuId = null;



    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'sorties')]
    private Collection $users;

    public function __construct()
    {
        $this->participationId = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDateHeureDebut(): ?\DateTimeInterface
    {
        return $this->dateHeureDebut;
    }

    public function setDateHeureDebut(\DateTimeInterface $dateHeureDebut): static
    {
        $this->dateHeureDebut = $dateHeureDebut;

        return $this;
    }

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(int $duree): static
    {
        $this->duree = $duree;

        return $this;
    }

    public function getDateLimiteInscription(): ?\DateTimeInterface
    {
        return $this->dateLimiteInscription;
    }

    public function setDateLimiteInscription(\DateTimeInterface $dateLimiteInscription): static
    {
        $this->dateLimiteInscription = $dateLimiteInscription;

        return $this;
    }

    public function getNbInscriptionMax(): ?string
    {
        return $this->nbInscriptionMax;
    }

    public function setNbInscriptionMax(string $nbInscriptionMax): static
    {
        $this->nbInscriptionMax = $nbInscriptionMax;

        return $this;
    }

    public function getEtatId(): ?Etat
    {
        return $this->etatId;
    }

    public function setEtatId(?Etat $etatId): static
    {
        $this->etatId = $etatId;

        return $this;
    }

    public function getLieuId(): ?Lieu
    {
        return $this->lieuId;
    }

    public function setLieuId(?Lieu $lieuId): static
    {
        $this->lieuId = $lieuId;

        return $this;
    }

    /**
     * @return Collection<int, Participant>
     */
    public function getParticipationId(): Collection
    {
        return $this->participationId;
    }

    public function addParticipationId(Participant $participationId): static
    {
        if (!$this->participationId->contains($participationId)) {
            $this->participationId->add($participationId);
        }

        return $this;
    }

    public function removeParticipationId(Participant $participationId): static
    {
        $this->participationId->removeElement($participationId);

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->addSorty($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            $user->removeSorty($this);
        }

        return $this;
    }
}

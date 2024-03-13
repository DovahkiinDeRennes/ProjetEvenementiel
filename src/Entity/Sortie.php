<?php

namespace App\Entity;

use App\Repository\SortieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SortieRepository::class)]
class Sortie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le nom de la sortie doit être renseigné.')]
    #[Assert\Length(max: 255, maxMessage: 'Le nom de la sortie ne doit pas dépasser {{ limit }} caractères.')]
    private ?string $nom = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotNull(message: 'La date et l\'heure de début doivent être renseignées.')]
    private ?\DateTimeInterface $dateHeureDebut = null;

    #[ORM\Column]
    #[Assert\NotNull(message: 'La duree de la sortie doit être renseignée.')]
    #[Assert\GreaterThan(0, message: 'La duree de la sortie doit être supérieure à 0.')]
    private ?int $duree = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotNull(message: 'La date limite d\'inscription doit être renseignée.')]
    private ?\DateTimeInterface $dateLimiteInscription = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotNull(message: 'Le nombre maximum d\'inscrits doit être renseigné.')]
    #[Assert\GreaterThan(0)]
    private ?int $nbInscriptionMax = null;

    #[ORM\ManyToOne(inversedBy: 'sorties')]
    private ?Etat $etatId = null;

    #[ORM\ManyToOne(inversedBy: 'sorties')]
    #[Assert\NotNull(message: 'Le lieu de la sortie doit être renseigné.')]
    private ?Lieu $lieuId = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(max: 255, maxMessage: 'La description de la sortie ne doit pas dépasser {{ limit }} caractères.')]
    #[Assert\NotBlank(message: 'La description de la sortie ne doit pas être vide.')]
    private ?string $description = null;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'sorties')]
    private Collection $users;

    #[ORM\ManyToOne(inversedBy: 'sortiesOrganisees')]
    private ?User $organisateur = null;


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


    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

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

    public function getOrganisateur(): ?User
    {
        return $this->organisateur;
    }

    public function setOrganisateur(?User $organisateur): void
    {
        $this->organisateur = $organisateur;
    }


}


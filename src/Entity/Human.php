<?php

namespace App\Entity;

use App\Repository\HumanRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use SebastianBergmann\CodeCoverage\InvalidArgumentException;

#[ORM\Entity(repositoryClass: HumanRepository::class)]
class Human
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $firstname = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lastname = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $middlename = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $maidenname = null;

    #[ORM\Column(length: 255)]
    private ?string $gender = null;

    #[ORM\Column(nullable: true)]
    private ?int $year_birth = null;

    #[ORM\Column(nullable: true)]
    private ?int $month_birth = null;

    #[ORM\Column(nullable: true)]
    private ?int $day_birth = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $picture = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $datetime_updated = null;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'children')]
    private ?self $mother = null;

    #[ORM\OneToMany(mappedBy: 'mother', targetEntity: self::class)]
    private Collection $children;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'children')]
    private ?self $father = null;

    #[ORM\Column(nullable: true)]
    private ?int $year_death = null;

    #[ORM\Column(nullable: true)]
    private ?int $month_death = null;

    #[ORM\Column(nullable: true)]
    private ?int $day_death = null;

    public function __construct()
    {
        $this->children = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getMiddlename(): ?string
    {
        return $this->middlename;
    }

    public function setMiddlename(?string $middlename): self
    {
        $this->middlename = $middlename;

        return $this;
    }

    public function getFullname(): ?string
    {
return $this->lastname. ' '. $this->firstname. ' '. $this->middlename . ' '. $this->year_birth;

    }

    public function getMaidenname(): ?string
    {
        return $this->maidenname;
    }

    public function setMaidenname(?string $maidenname): self
    {
        $this->maidenname = $maidenname;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(string $gender): self
    {
        if($gender!='male' && $gender!='female'){
            throw new InvalidArgumentException('Gender field must be \'male\' or \'female\'');
        }
        $this->gender = $gender;

        return $this;
    }

    public function getYearBirth(): ?int
    {
        return $this->year_birth;
    }

    public function setYearBirth(?int $year_birth): self
    {
        $this->year_birth = $year_birth;

        return $this;
    }

    public function getMonthBirth(): ?int
    {
        return $this->month_birth;
    }

    public function setMonthBirth(?int $month_birth): self
    {
        $this->month_birth = $month_birth;

        return $this;
    }

    public function getDayBirth(): ?int
    {
        return $this->day_birth;
    }

    public function setDayBirth(?int $day_birth): self
    {
        $this->day_birth = $day_birth;

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDatetimeUpdated(): ?\DateTimeInterface
    {
        return $this->datetime_updated;
    }

    public function setDatetimeUpdated(\DateTimeInterface $datetime_updated): self
    {
        $this->datetime_updated = $datetime_updated;

        return $this;
    }

    public function getMother(): ?self
    {
        return $this->mother;
    }

    public function setMother(?self $mother): self
    {
        if($mother==null){
            $this->mother = $mother;
            return $this;
        }
        if($mother->getGender()=='female' && $this!=$mother)
        $this->mother = $mother;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addChild(self $child): self
    {
        if (!$this->children->contains($child)) {
            
            $this->children->add($child);
            if($this->getGender()=='male')
            $child->setFather($this);
            else
            $child->setMother($this);
        }

        return $this;
    }

    public function removeChild(self $child): self
    {
        if ($this->children->removeElement($child)) {
            // set the owning side to null (unless already changed)
            if ($child->getMother() === $this) {
                $child->setMother(null);
            }
            if ($child->getFather() === $this) {
                $child->setFather(null);
            }
        }

        return $this;
    }

    public function getFather(): ?self
    {
        return $this->father;
    }

    public function setFather(?self $father): self
    {
        if($father==null){
            $this->father = $father;
            return $this;
        }
        if($father->getGender()=='male' && $this!=$father)
        $this->father = $father;

        return $this;
    }

    public function __toString() 
{
    return (string) $this->id; 
}

    public function getYearDeath(): ?int
    {
        return $this->year_death;
    }

    public function setYearDeath(?int $year_death): self
    {
        $this->year_death = $year_death;

        return $this;
    }

    public function getMonthDeath(): ?int
    {
        return $this->month_death;
    }

    public function setMonthDeath(?int $month_death): self
    {
        $this->month_death = $month_death;

        return $this;
    }

    public function getDayDeath(): ?int
    {
        return $this->day_death;
    }

    public function setDayDeath(?int $day_death): self
    {
        $this->day_death = $day_death;

        return $this;
    }
}




<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\KidRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: KidRepository::class)]
class Kid
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank]
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private bool $active = true;

    #[ORM\ManyToOne(inversedBy: 'kids')]
    private ?KidGroup $kidGroup = null;

    /**
     * @var Collection<int, Guardian>
     */
    #[ORM\ManyToMany(targetEntity: Guardian::class, mappedBy: 'kids', fetch: 'EXTRA_LAZY')]
    private Collection $guardians;

    /**
     * @var Collection<int, Appointment>
     */
    #[ORM\ManyToMany(targetEntity: Appointment::class, mappedBy: 'attendees', fetch: 'EXTRA_LAZY')]
    private Collection $appointments;

    public function __construct()
    {
        $this->guardians = new ArrayCollection();
        $this->appointments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;

        return $this;
    }

    public function getKidGroup(): ?KidGroup
    {
        return $this->kidGroup;
    }

    public function setKidGroup(?KidGroup $kidGroup): static
    {
        $this->kidGroup = $kidGroup;

        return $this;
    }

    /**
     * @return Collection<int, Guardian>
     */
    public function getGuardians(?bool $active = null): Collection
    {
        if (!is_null($active)) {
            return $this->guardians->matching(
                Criteria::create()->where(Criteria::expr()->eq('active', $active))
            );
        }

        return $this->guardians;
    }

    public function addGuardian(Guardian $guardian): static
    {
        if (!$this->guardians->contains($guardian)) {
            $this->guardians->add($guardian);
            $guardian->addKid($this);
        }

        return $this;
    }

    public function removeGuardian(Guardian $guardian): static
    {
        if ($this->guardians->removeElement($guardian)) {
            $guardian->removeKid($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Appointment>
     */
    public function getAppointments(?bool $active = null): Collection
    {
        if (!is_null($active)) {
            return $this->appointments->matching(
                Criteria::create()->where(Criteria::expr()->eq('active', $active))
            );
        }

        return $this->appointments;
    }

    public function addAppointment(Appointment $appointment): static
    {
        if (!$this->appointments->contains($appointment)) {
            $this->appointments->add($appointment);
            $appointment->addAttendee($this);
        }

        return $this;
    }

    public function removeAppointment(Appointment $appointment): static
    {
        if ($this->appointments->removeElement($appointment)) {
            $appointment->removeAttendee($this);
        }

        return $this;
    }
}

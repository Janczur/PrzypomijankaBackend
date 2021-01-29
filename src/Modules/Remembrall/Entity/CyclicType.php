<?php

namespace App\Modules\Remembrall\Entity;

use App\Modules\Remembrall\Repository\CyclicTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CyclicTypeRepository::class)
 */
class CyclicType
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("reminder:read")
     * @Assert\Choice(callback={"App\Modules\Remembrall\Abstracts\CyclicTypes", "getTypeNames"})
     */
    private ?string $name;

    /**
     * @ORM\OneToMany(targetEntity=Cyclic::class, mappedBy="type")
     */
    private Collection $cyclics;

    public function __construct()
    {
        $this->cyclics = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|Cyclic[]
     */
    public function getCyclics(): Collection
    {
        return $this->cyclics;
    }

    public function addCyclic(Cyclic $cyclic): self
    {
        if (!$this->cyclics->contains($cyclic)) {
            $this->cyclics[] = $cyclic;
            $cyclic->setType($this);
        }

        return $this;
    }

    public function removeCyclic(Cyclic $cyclic): self
    {
        if ($this->cyclics->removeElement($cyclic)) {
            // set the owning side to null (unless already changed)
            if ($cyclic->getType() === $this) {
                $cyclic->setType(null);
            }
        }

        return $this;
    }
}

<?php

namespace App\Modules\Remembrall\Entity;

use App\Modules\Remembrall\Repository\CyclicRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CyclicRepository::class)
 */
class Cyclic
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\ManyToOne(targetEntity=CyclicType::class, inversedBy="cyclics", fetch="LAZY")
     * @ORM\JoinColumn(nullable=false)
     * @Groups("reminder:read")
     */
    private CyclicType $type;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Positive()
     * @Groups("reminder:read")
     */
    private int $periodicity;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): CyclicType
    {
        return $this->type;
    }

    public function setType(CyclicType $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getPeriodicity(): int
    {
        return $this->periodicity;
    }

    public function setPeriodicity(int $periodicity): self
    {
        $this->periodicity = $periodicity;

        return $this;
    }
}

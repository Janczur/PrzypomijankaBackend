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
    public const DAY = 1;
    public const WEEK = 2;
    public const MONTH = 3;
    public const YEAR = 4;

    public const TYPES = [
        self::DAY,
        self::WEEK,
        self::MONTH,
        self::YEAR
    ];

    public array $typeNames = [
        self::DAY => 'Day',
        self::WEEK => 'Week',
        self::MONTH => 'Month',
        self::YEAR => 'Year',
    ];

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Positive()
     * @Groups("reminder:read")
     */
    private int $periodicity;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Choice(choices=self::TYPES)
     * @Groups("reminder:read")
     */
    private int $type_id;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getTypeId(): int
    {
        return $this->type_id;
    }

    public function setTypeId(int $type_id): self
    {
        $this->type_id = $type_id;
        return $this;
    }

    public function getFirstLetterOfTypeName(): string
    {
        return $this->getTypeName()[0];
    }

    public function getTypeName(): string
    {
        return $this->typeNames[$this->type_id];
    }
}

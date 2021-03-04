<?php

namespace App\Modules\Remembrall\Entity;

use App\Modules\Remembrall\Repository\PreReminderRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=PreReminderRepository::class)
 */
class PreReminder
{

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
    private int $days_before;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Type("DateTimeInterface")
     * @Groups("reminder:read")
     */
    private DateTimeInterface $remind_at;

    /**
     * @ORM\OneToOne(targetEntity=Reminder::class, mappedBy="pre_reminder")
     */
    private ?Reminder $reminder;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDaysBefore(): int
    {
        return $this->days_before;
    }

    public function setDaysBefore(int $days_before): self
    {
        $this->days_before = $days_before;

        return $this;
    }

    public function getRemindAt(): DateTimeInterface
    {
        return $this->remind_at;
    }

    public function setRemindAt(DateTimeInterface $remind_at): self
    {
        $this->remind_at = $remind_at;

        return $this;
    }

    public function getReminder(): ?Reminder
    {
        return $this->reminder;
    }

    public function setReminder(?Reminder $reminder): self
    {
        // unset the owning side of the relation if necessary
        if ($reminder === null && $this->reminder !== null) {
            $this->reminder->setPreReminder(null);
        }

        // set the owning side of the relation if necessary
        if ($reminder !== null && $reminder->getPreReminder() !== $this) {
            $reminder->setPreReminder($this);
        }

        $this->reminder = $reminder;

        return $this;
    }
}

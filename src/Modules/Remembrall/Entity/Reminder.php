<?php

namespace App\Modules\Remembrall\Entity;

use App\Modules\Remembrall\Repository\ReminderRepository;
use App\Modules\Security\Entity\User;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ReminderRepository::class)
 */
class Reminder
{
    const SUPPORTED_CHANNELS = ['email', 'sms'];
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("reminder:read")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups("reminder:read")
     * @Assert\Length(min=3,max=255)
     */
    private ?string $title;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups("reminder:read")
     * @Assert\Length(min=3,max=255)
     */
    private ?string $description;

    /**
     * @ORM\OneToOne(targetEntity=Cyclic::class, cascade={"persist", "remove"}, fetch="LAZY")
     * @Groups("reminder:read")
     */
    private ?Cyclic $cyclic;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="reminders")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?UserInterface $user;

    /**
     * @ORM\Column(type="datetime")
     * @Groups("reminder:read")
     * @Assert\NotBlank()
     * @Assert\GreaterThan("tomorrow +30 minutes")
     */
    private ?DateTimeInterface $remind_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups("reminder:read")
     * @Assert\GreaterThan("tomorrow +30 minutes")
     */
    private ?DateTimeInterface $pre_remind_at = null;

    /**
     * @ORM\Column(type="boolean")
     * @Groups("reminder:read")
     */
    private ?bool $pre_reminded = false;


    /**
     * @ORM\Column(type="simple_array")
     * @Groups("reminder:read")
     * @Assert\Choice(choices=self::SUPPORTED_CHANNELS, multiple=true)
     */
    private array $channels = [];

    /**
     * @ORM\Column(type="boolean")
     * @Assert\Type("boolean")
     * @Groups("reminder:read")
     */
    private ?bool $active = true;

    /**
     * @ORM\Column(type="datetime")
     * @Groups("reminder:read")
     */
    private ?DateTimeInterface $created_at;

    public function __construct()
    {
        $this->setCreatedAt(new \DateTime());
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

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

    public function getCyclic(): ?Cyclic
    {
        return $this->cyclic;
    }

    public function setCyclic(?Cyclic $cyclic): self
    {
        $this->cyclic = $cyclic;

        return $this;
    }

    public function getRemindAt(): ?DateTimeInterface
    {
        return $this->remind_at;
    }

    public function setRemindAt(DateTimeInterface $remind_at): self
    {
        $this->remind_at = $remind_at;

        return $this;
    }

    public function getPreRemindAt(): ?\DateTimeInterface
    {
        return $this->pre_remind_at;
    }

    public function setPreRemindAt(?\DateTimeInterface $pre_remind_at): self
    {
        $this->pre_remind_at = $pre_remind_at;

        return $this;
    }

    public function getPreReminded(): ?bool
    {
        return $this->pre_reminded;
    }

    public function setPreReminded(bool $pre_reminded): self
    {
        $this->pre_reminded = $pre_reminded;

        return $this;
    }

    public function getOwner(): ?UserInterface
    {
        return $this->user;
    }

    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    public function setUser(?UserInterface $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getChannels(): ?array
    {
        return $this->channels;
    }

    public function setChannels(array $channels): self
    {
        $this->channels = $channels;

        return $this;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function isCyclic(): bool
    {
        return $this->cyclic ? true : false;
    }
}

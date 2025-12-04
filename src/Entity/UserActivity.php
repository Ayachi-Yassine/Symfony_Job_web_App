<?php

namespace App\Entity;

use App\Repository\UserActivityRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserActivityRepository::class)]
#[ORM\Table(name: 'user_activities')]
class UserActivity
{
    const ACTION_LOGIN = 'login';
    const ACTION_LOGOUT = 'logout';
    const ACTION_REGISTER = 'register';
    const ACTION_PROFILE_UPDATE = 'profile_update';
    const ACTION_CV_UPLOAD = 'cv_upload';
    const ACTION_JOB_APPLICATION = 'job_application';
    const ACTION_PROFILE_VIEW = 'profile_view';
    const ACTION_PASSWORD_CHANGE = 'password_change';
    const ACTION_WITHDRAW_APPLICATION = 'withdraw_application';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'activities')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $user = null;

    #[ORM\Column(type: 'string', length: 50)]
    private string $action = '';

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'string', length: 45, nullable: true)]
    private ?string $ipAddress = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $userAgent = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $relatedEntityId = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $relatedEntityType = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $createdAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function setAction(string $action): self
    {
        $this->action = $action;
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

    public function getIpAddress(): ?string
    {
        return $this->ipAddress;
    }

    public function setIpAddress(?string $ipAddress): self
    {
        $this->ipAddress = $ipAddress;
        return $this;
    }

    public function getUserAgent(): ?string
    {
        return $this->userAgent;
    }

    public function setUserAgent(?string $userAgent): self
    {
        $this->userAgent = $userAgent;
        return $this;
    }

    public function getRelatedEntityId(): ?int
    {
        return $this->relatedEntityId;
    }

    public function setRelatedEntityId(?int $relatedEntityId): self
    {
        $this->relatedEntityId = $relatedEntityId;
        return $this;
    }

    public function getRelatedEntityType(): ?string
    {
        return $this->relatedEntityType;
    }

    public function setRelatedEntityType(?string $relatedEntityType): self
    {
        $this->relatedEntityType = $relatedEntityType;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public static function getActionLabels(): array
    {
        return [
            self::ACTION_LOGIN => 'Login',
            self::ACTION_LOGOUT => 'Logout',
            self::ACTION_REGISTER => 'Account Created',
            self::ACTION_PROFILE_UPDATE => 'Profile Updated',
            self::ACTION_CV_UPLOAD => 'CV Uploaded',
            self::ACTION_JOB_APPLICATION => 'Applied for Job',
            self::ACTION_PROFILE_VIEW => 'Profile Viewed',
            self::ACTION_PASSWORD_CHANGE => 'Password Changed',
            self::ACTION_WITHDRAW_APPLICATION => 'Application Withdrawn',
        ];
    }

    public function getActionLabel(): string
    {
        return self::getActionLabels()[$this->action] ?? $this->action;
    }
}

<?php
declare(strict_types=1);

namespace EonX\EasyBatch\Interfaces;

interface BatchObjectInterface
{
    /**
     * @var string
     */
    public const DATETIME_FORMAT = 'Y-m-d H:i:s';

    /**
     * @var string[]
     */
    public const DATE_TIMES = [
        'cancelled_at' => 'setCancelledAt',
        'finished_at' => 'setFinishedAt',
        'started_at' => 'setStartedAt',
        'created_at' => 'setCreatedAt',
        'updated_at' => 'setUpdatedAt',
    ];

    /**
     * @var string
     */
    public const STATUS_CANCELLED = 'cancelled';

    /**
     * @var string
     */
    public const STATUS_FAILED = 'failed';

    /**
     * @var string
     */
    public const STATUS_PENDING = 'pending';

    /**
     * @var string
     */
    public const STATUS_PROCESSING = 'processing';

    /**
     * @var string
     */
    public const STATUS_SUCCESS = 'success';

    /**
     * @var string
     */
    public const STATUS_SUCCESS_PENDING_APPROVAL = 'success_pending_approval';

    public function getCancelledAt(): ?\DateTimeInterface;

    public function getCreatedAt(): \DateTimeInterface;

    public function getFinishedAt(): ?\DateTimeInterface;

    /**
     * @return null|int|string
     */
    public function getId();

    public function getStartedAt(): ?\DateTimeInterface;

    public function getStatus(): string;

    public function getThrowable(): ?\Throwable;

    public function getUpdatedAt(): \DateTimeInterface;

    public function setCancelledAt(\DateTimeInterface $cancelledAt): self;

    public function setCreatedAt(\DateTimeInterface $createdAt): self;

    public function setFinishedAt(\DateTimeInterface $finishedAt): self;

    /**
     * @param int|string $id
     */
    public function setId($id): self;

    public function setStartedAt(\DateTimeInterface $startedAt): self;

    public function setStatus(string $status): self;

    public function setThrowable(\Throwable $throwable): self;

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self;

    /**
     * @return mixed[]
     */
    public function toArray(): array;
}

<?php
declare(strict_types=1);

namespace EonX\EasyEntityChange\Tests\Stubs;

use EonX\EasyEntityChange\Interfaces\DeletedEntityEnrichmentInterface;

/**
 * @coversNothing
 */
class DeletedEntityEnrichmentStub implements DeletedEntityEnrichmentInterface
{
    /**
     * @var mixed[][]
     */
    private $metadata;

    /**
     * Constructor
     *
     * @param mixed[][] $metadata
     */
    public function __construct(array $metadata)
    {
        $this->metadata = $metadata;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata(object $entity): array
    {
        return \array_shift($this->metadata) ?: [];
    }
}

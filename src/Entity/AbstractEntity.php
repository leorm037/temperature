<?php

namespace App\Entity;

use App\Helper\DateTimeHelper;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Factory\UuidFactory;
use Symfony\Component\Uid\Uuid;


abstract class AbstractEntity
{

    #[ORM\PrePersist]
    public function prePersist(): void
    {
        $this->createdAt();
        $this->generateUuid();
    }

    #[ORM\PreUpdate]
    public function preUpdate(): void
    {
        $this->updatedAt();
    }

    private function createdAt(): void
    {
        if (property_exists(get_class($this), "createdAt") && null === $this->createdAt) {
            $this->createdAt = DateTimeHelper::currentDateTimeImmutableUTC();
        }
    }

    private function updatedAt(): void
    {
        if (property_exists(get_class($this), "updatedAt")) {
            $this->updatedAt = DateTimeHelper::currentDateTimeUTC();
        }
    }
    
    private function generateUuid(): void
    {
        if (property_exists(get_class($this), "uuid") && null === $this->uuid) {
            
            /** @var UuidFactory $uuidFactory */
            $uuidFactory = Uuid::v7();
            
            $this->uuid = $uuidFactory->create();
        }
    }

}

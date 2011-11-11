<?php

namespace F5\Bundle\ZetaBundle\EventListener;

use \ezcSearchSession;
use \ezcBasePersistable;
use Doctrine\ORM\Event\LifecycleEventArgs;

class EzcSearchListener
{
    protected $searchSession;

    public function __construct(ezcSearchSession $searchSession)
    {
        $this->searchSession = $searchSession;
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        if ($args->getEntity() instanceof ezcBasePersistable) {
            $this->searchSession->index($args->getEntity());
        }
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        if ($args->getEntity() instanceof ezcBasePersistable) {
            $this->searchSession->index($args->getEntity());
        }
    }

    public function preDelete(LifecycleEventArgs $args)
    {
        if ($args->getEntity() instanceof ezcBasePersistable) {
            $this->searchSession->deleteById($args->getEntity()->getId(),get_class($args->getEntity()));
        }
    }
}

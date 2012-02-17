<?php
namespace F5\Bundle\ZetaBundle\Subscriber;

use Symfony\Component\Finder\Finder;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Knp\Component\Pager\Event\ItemsEvent;

class PaginateZetaQuerySubscriber implements EventSubscriberInterface
{
    protected $searchSession;

    public function __construct($searchSession){
        $this->searchSession = $searchSession;
    }

    public function items(ItemsEvent $event)
    {
        if ($event->target instanceof \ezcSearchFindQuery) {
            $query = $event->target;
            $query->limit($event->getLimit(),$event->getOffset());

            $items = $this->searchSession->find($query);

            $event->count = $items->resultCount;
            $event->items = $items->documents;
            //FIXME: Horrible horrible hack to pass the facets back
            $event->target->facets = $items->facets;
            $event->target->facet_queries = $items->facet_queries;
            $event->stopPropagation();
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            'knp_pager.items' => array('items', 1)
        );
    }
}
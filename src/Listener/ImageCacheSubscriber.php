<?php


namespace App\Listener;


use App\Entity\Trick;
use App\Service\UploaderHelper;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageCacheSubscriber implements EventSubscriber
{

    /**
     * @var UploaderHelper
     */
    private $uploaderHelper;

    /**
     * @var CacheManager
     */
    private $cacheManager;

    public function __construct(CacheManager $cacheManager, UploaderHelper $uploaderHelper)
    {
        $this->uploaderHelper  = $uploaderHelper;
        $this->cacheManager = $cacheManager;
    }

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return string[]
     */
    public function getSubscribedEvents(): array
    {
      return [
          'preRemove',
          'preUpdate',
      ];
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof Trick){
            return;
        }

        $this->cacheManager->remove($this->uploaderHelper->getPublicPath($entity->getImagePath()));

    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();

       if (!$entity instanceof Trick){
           return;
       }

       if ( $entity->getImageFilename() instanceof UploadedFile){
            $this->cacheManager->remove($this->uploaderHelper->getPublicPath($entity->getImagePath()));
       }

    }
}
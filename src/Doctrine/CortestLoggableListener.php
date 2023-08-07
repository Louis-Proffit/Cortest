<?php

namespace App\Doctrine;

use Doctrine\Persistence\ObjectManager;
use Gedmo\Loggable\LoggableListener;

class CortestLoggableListener extends LoggableListener
{

    protected function getObjectChangeSetData($ea, $object, $logEntry)
    {
        $changeSetData = parent::getObjectChangeSetData($ea, $object, $logEntry);
        if (empty($changeSetData)) {
            return ["id" => 0];
        } else {
            return $changeSetData;
        }
    }

}
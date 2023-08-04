<?php

namespace App\Core\Activite;

use Gedmo\Loggable\Entity\LogEntry;

class LogEntryWrapper
{

    public LogEntry $entry;
    public string $class;
    public string $action;
    public string|null $lien;
    public object|null $object;
    public string $message;

    /**
     * @param LogEntry $entry
     * @param string $class
     * @param string $action
     * @param string|null $lien
     * @param object|null $object
     * @param string $message
     */
    public function __construct(LogEntry $entry, string $class, string $action, ?string $lien, ?object $object, string $message)
    {
        $this->entry = $entry;
        $this->class = $class;
        $this->action = $action;
        $this->lien = $lien;
        $this->object = $object;
        $this->message = $message;
    }
}
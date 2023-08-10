<?php

namespace App\Tests\Core\Activite;

use App\Core\Activite\LogEntryProcessor;
use PHPUnit\Framework\TestCase;
use function PHPUnit\Framework\assertSameSize;

class LogEntryProcessorTest extends TestCase {


    public function testActionContent(): void
    {
        assertSameSize(LogEntryProcessor::ACTIONS, LogEntryProcessor::ACTION_INFOS);
        assertSameSize(LogEntryProcessor::ACTIONS, LogEntryProcessor::ACTION_INFOS);
    }

    public function testClassContent(): void
    {
        assertSameSize(LogEntryProcessor::CLASSES, LogEntryProcessor::CLASS_NAMES);
        assertSameSize(LogEntryProcessor::CLASSES, LogEntryProcessor::CLASS_INFOS);
    }
}
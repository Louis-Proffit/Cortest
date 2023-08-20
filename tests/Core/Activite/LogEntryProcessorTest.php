<?php

namespace App\Tests\Core\Activite;

use App\Core\Activite\CortestLogEntryProcessor;
use PHPUnit\Framework\TestCase;
use function PHPUnit\Framework\assertSameSize;

class LogEntryProcessorTest extends TestCase {


    public function testActionContent(): void
    {
        assertSameSize(CortestLogEntryProcessor::ACTIONS, CortestLogEntryProcessor::ACTION_INFOS);
        assertSameSize(CortestLogEntryProcessor::ACTIONS, CortestLogEntryProcessor::ACTION_INFOS);
    }

    public function testClassContent(): void
    {
        assertSameSize(CortestLogEntryProcessor::CLASSES, CortestLogEntryProcessor::CLASS_NAMES);
        assertSameSize(CortestLogEntryProcessor::CLASSES, CortestLogEntryProcessor::CLASS_INFOS);
    }
}
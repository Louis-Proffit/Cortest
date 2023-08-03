<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;

trait CortestTestTrait
{

    private KernelBrowser $client;


    protected function initClient(): void
    {
        $this->client = self::createClient();
    }
}
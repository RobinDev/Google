<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use rOpenDev\Google\SearchViaCurl;

final class SearchTest extends TestCase
{
    public function testResultViaCurl(): void
    {
        $Google = new SearchViaCurl('piedweb.com');
        $Google
                 ->setNbrPage(1) // Get Only first Page
                 ->setTld('fr')
                 ->setLanguage('fr')
                 ->setSleep(1)
                 ->setCacheFolder(null)
        ;
        $results = $Google->extractResults();

        $this->assertEquals('https://piedweb.com/', $results[0]['link']);
    }

    public function testResultViaCurlForMobile(): void
    {
        $Google = new SearchViaCurl('piedweb.com');
        $Google
                 ->setNbrPage(1) // Get Only first Page
                 ->setTld('fr')
                 ->setLanguage('fr')
                 ->setSleep(1)
                 ->setMobile(true)
                 ->setCacheFolder(null)
        ;
        $results = $Google->extractResults();

        $this->assertEquals('https://piedweb.com/', $results[0]['link']);
    }
}

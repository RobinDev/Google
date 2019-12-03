<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use rOpenDev\Qwant\QwantSearchViaCurl;

final class SearchTest extends TestCase
{
    public function testResultViaCurl(): void
    {
        $Qwant = new QwantSearchViaCurl('piedweb.com');
        $Qwant->setNbrPage(1) // Get Only first Page
                 ->setCacheFolder(null)
        ;
        $results =  $Qwant->extractResults();

        $this->assertEquals('https://piedweb.com/', $results[0]['link']);
    }

}


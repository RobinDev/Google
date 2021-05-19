<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use rOpenDev\Google\SafeBrowsing;

final class SafeBrowsingTest extends TestCase
{
    public function testResult(): void
    {
        $result = SafeBrowsing::get('https://piedweb.com');

        $this->assertTrue($result);
    }
}

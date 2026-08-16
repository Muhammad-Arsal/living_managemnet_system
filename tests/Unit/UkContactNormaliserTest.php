<?php

namespace Tests\Unit;

use App\Support\UkContactNormaliser;
use PHPUnit\Framework\TestCase;

class UkContactNormaliserTest extends TestCase
{
    public function test_normalises_uk_postcode(): void
    {
        $this->assertSame('SW1A 1AA', UkContactNormaliser::postcode('sw1a1aa'));
        $this->assertSame('M1 1AE', UkContactNormaliser::postcode('m1 1ae'));
    }

    public function test_normalises_uk_mobile(): void
    {
        $this->assertSame('07123456789', UkContactNormaliser::mobile('+44 7123 456789'));
        $this->assertSame('07123456789', UkContactNormaliser::mobile('07123 456 789'));
    }
}

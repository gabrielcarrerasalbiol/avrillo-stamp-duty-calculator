<?php

namespace Tests\Unit;

use App\Services\StampDutyCalculator;
use Tests\TestCase;

class StampDutyCalculatorTest extends TestCase
{
    public function test_it_calculates_standard_residential_sdlt(): void
    {
        $result = app(StampDutyCalculator::class)->calculate([
            'purchase_price' => '295000',
            'is_first_time_buyer' => false,
            'owns_additional_property' => false,
        ]);

        $this->assertSame('standard', $result['scenario']);
        $this->assertSame(475000, $result['total_tax_pence']);
        $this->assertSame(1.61, $result['effective_rate']);
    }

    public function test_it_applies_first_time_buyer_relief(): void
    {
        $result = app(StampDutyCalculator::class)->calculate([
            'purchase_price' => '425000',
            'is_first_time_buyer' => true,
            'owns_additional_property' => false,
        ]);

        $this->assertSame('first_time_buyer', $result['scenario']);
        $this->assertSame(625000, $result['total_tax_pence']);
        $this->assertSame('First-time buyer relief applied', $result['headline']);
    }

    public function test_it_adds_the_additional_property_surcharge(): void
    {
        $result = app(StampDutyCalculator::class)->calculate([
            'purchase_price' => '300000',
            'is_first_time_buyer' => false,
            'owns_additional_property' => true,
        ]);

        $this->assertSame('additional_property', $result['scenario']);
        $this->assertSame(2000000, $result['total_tax_pence']);
        $this->assertCount(2, $result['groups']);
        $this->assertSame(1500000, $result['groups'][1]['total_tax_pence']);
    }

    public function test_it_handles_an_exact_band_boundary(): void
    {
        $result = app(StampDutyCalculator::class)->calculate([
            'purchase_price' => '250000',
            'is_first_time_buyer' => false,
            'owns_additional_property' => false,
        ]);

        $this->assertSame(250000, $result['total_tax_pence']);
        $this->assertSame('the portion from £125,001 to £250,000', $result['groups'][0]['rows'][1]['label']);
    }

    public function test_it_falls_back_to_standard_rates_when_first_time_buyer_relief_does_not_apply(): void
    {
        $result = app(StampDutyCalculator::class)->calculate([
            'purchase_price' => '500001',
            'is_first_time_buyer' => true,
            'owns_additional_property' => false,
        ]);

        $this->assertSame('standard', $result['scenario']);
        $this->assertSame(1500005, $result['total_tax_pence']);
        $this->assertStringContainsString('£500,000 or less', $result['notes'][0]);
    }

    public function test_it_does_not_apply_the_surcharge_below_its_threshold(): void
    {
        $result = app(StampDutyCalculator::class)->calculate([
            'purchase_price' => '39999',
            'is_first_time_buyer' => false,
            'owns_additional_property' => true,
        ]);

        $this->assertSame(0, $result['total_tax_pence']);
        $this->assertCount(1, $result['groups']);
        $this->assertStringContainsString('does not apply below £40,000', $result['notes'][0]);
    }
}
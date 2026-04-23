<?php

namespace Tests\Feature;

use Tests\TestCase;

class StampDutyCalculatorPageTest extends TestCase
{
    public function test_get_calculate_redirects_to_the_main_page(): void
    {
        $response = $this->get('/calculate');

        $response->assertRedirect('/');
    }

    public function test_the_calculator_page_loads(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Stamp duty without the tax-code wording.');
    }

    public function test_a_valid_submission_returns_a_breakdown(): void
    {
        $response = $this->postToCalculator([
            'purchase_price' => '295000',
            'buyer_type' => 'standard',
            'owns_additional_property' => '0',
        ]);

        $response->assertOk();
        $response->assertSee('£4,750.00', false);
        $response->assertSee('the portion from £250,001 to £925,000');
        $response->assertSee('Effective rate');
    }

    public function test_invalid_input_is_returned_with_clear_messages(): void
    {
        $response = $this->from('/')->postToCalculator([
            'purchase_price' => 'abc',
            'buyer_type' => 'standard',
            'owns_additional_property' => '0',
        ]);

        $response->assertRedirect('/');
        $response->assertSessionHasErrors(['purchase_price']);
    }

    public function test_first_time_buyer_and_additional_property_selection_still_returns_a_result_with_explanation(): void
    {
        $response = $this->postToCalculator([
            'purchase_price' => '300000',
            'buyer_type' => 'first_time_buyer',
            'owns_additional_property' => '1',
        ]);

        $response->assertOk();
        $response->assertSee('£20,000.00', false);
        $response->assertSee('First-time buyer relief cannot be used with an additional property purchase');
    }

    private function postToCalculator(array $payload)
    {
        return $this
            ->withSession(['_token' => 'testing-token'])
            ->post('/calculate', array_merge($payload, ['_token' => 'testing-token']));
    }
}
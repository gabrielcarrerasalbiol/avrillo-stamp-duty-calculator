<?php

namespace App\Http\Controllers;

use App\Services\StampDutyCalculator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StampDutyCalculatorController extends Controller
{
    public function index()
    {
        return view('calculator');
    }

    public function calculate(Request $request, StampDutyCalculator $calculator)
    {
        $input = $request->all();
        $input['purchase_price'] = $this->normalizePurchasePrice((string) $request->input('purchase_price', ''));

        $validator = Validator::make(
            $input,
            [
                'purchase_price' => ['required', 'numeric', 'decimal:0,2', 'min:0.01'],
                'buyer_type' => ['required', 'in:standard,first_time_buyer'],
                'owns_additional_property' => ['nullable', 'boolean'],
            ],
            [
                'purchase_price.required' => 'Enter the property price.',
                'purchase_price.numeric' => 'The property price must be a number.',
                'purchase_price.decimal' => 'Use at most 2 decimal places for the property price.',
                'purchase_price.min' => 'The property price must be more than £0.',
                'buyer_type.required' => 'Choose whether all buyers are first-time buyers.',
                'buyer_type.in' => 'Choose a valid buyer status option.',
                'owns_additional_property.boolean' => 'Choose a valid option for additional property rates.',
            ]
        );

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();

        $result = $calculator->calculate([
            'purchase_price' => (string) $validated['purchase_price'],
            'is_first_time_buyer' => $validated['buyer_type'] === 'first_time_buyer',
            // Judgement call: this checkbox means the buyer knows the higher-rate rules apply.
            'owns_additional_property' => (bool) ($validated['owns_additional_property'] ?? false),
        ]);

        return view('calculator', [
            'result' => $result,
            'submitted' => [
                'purchase_price' => $validated['purchase_price'],
                'buyer_type' => $validated['buyer_type'],
                'owns_additional_property' => (bool) ($validated['owns_additional_property'] ?? false),
            ],
        ]);
    }

    private function normalizePurchasePrice(string $value): string
    {
        $value = trim($value);

        if ($value === '') {
            return $value;
        }

        $value = preg_replace('/\s+/', '', $value) ?? $value;

        $hasDot = str_contains($value, '.');
        $hasComma = str_contains($value, ',');

        if ($hasDot && $hasComma) {
            $lastDot = strrpos($value, '.');
            $lastComma = strrpos($value, ',');

            $decimalSeparator = ($lastComma !== false && $lastComma > $lastDot) ? ',' : '.';
            $thousandsSeparator = $decimalSeparator === ',' ? '.' : ',';

            $value = str_replace($thousandsSeparator, '', $value);
            return str_replace($decimalSeparator, '.', $value);
        }

        if ($hasComma) {
            if (preg_match('/^\d{1,3}(,\d{3})+$/', $value) === 1) {
                return str_replace(',', '', $value);
            }

            return str_replace(',', '.', $value);
        }

        if ($hasDot) {
            if (preg_match('/^\d{1,3}(\.\d{3})+$/', $value) === 1) {
                return str_replace('.', '', $value);
            }
        }

        return $value;
    }
}
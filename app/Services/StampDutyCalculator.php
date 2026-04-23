<?php

namespace App\Services;

class StampDutyCalculator
{
    public function calculate(array $input): array
    {
        $priceInPence = $this->toPence($input['purchase_price']);
        $config = config('sdlt');

        $isFirstTimeBuyer = (bool) ($input['is_first_time_buyer'] ?? false);
        $ownsAdditionalProperty = (bool) ($input['owns_additional_property'] ?? false);

        $notes = [];
        $groups = [];

        if ($isFirstTimeBuyer && $ownsAdditionalProperty) {
            $isFirstTimeBuyer = false;
            $notes[] = 'First-time buyer relief cannot be used with an additional property purchase, so standard residential rates were used before applying the higher-rate surcharge.';
        }

        $baseBands = $config['standard']['bands'];
        $appliedScenario = 'standard';
        $headline = 'Standard residential SDLT';

        if ($isFirstTimeBuyer && $priceInPence <= $this->toPence($config['first_time_buyer']['max_price'])) {
            $baseBands = $config['first_time_buyer']['bands'];
            $appliedScenario = 'first_time_buyer';
            $headline = 'First-time buyer relief applied';
        } elseif ($isFirstTimeBuyer) {
            $notes[] = 'First-time buyer relief only applies when the purchase price is £500,000 or less, so standard residential rates were used.';
        }

        $baseBreakdown = $this->calculateBandBreakdown($priceInPence, $baseBands, false);

        $groups[] = [
            'title' => $headline,
            'rows' => $baseBreakdown['rows'],
            'total_tax_pence' => $baseBreakdown['total_tax_pence'],
        ];

        $surchargeTotal = 0;

        if ($ownsAdditionalProperty) {
            if ($priceInPence >= $this->toPence($config['additional_property']['minimum_price'])) {
                $appliedScenario = 'additional_property';
                $surchargeBreakdown = $this->calculateBandBreakdown(
                    $priceInPence,
                    $config['standard']['bands'],
                    true,
                    $config['additional_property']['rate_bps']
                );

                $groups[] = [
                    'title' => 'Additional property surcharge',
                    'rows' => $surchargeBreakdown['rows'],
                    'total_tax_pence' => $surchargeBreakdown['total_tax_pence'],
                ];

                $surchargeTotal = $surchargeBreakdown['total_tax_pence'];
                $notes[] = 'The higher-rate checkbox is treated as meaning you will still own another residential property worth £40,000 or more after completion and are not replacing your main home.';
            } else {
                $notes[] = 'The additional property surcharge does not apply below £40,000.';
            }
        }

        $totalTaxPence = $baseBreakdown['total_tax_pence'] + $surchargeTotal;
        $effectiveRate = $priceInPence > 0
            ? round(($totalTaxPence / $priceInPence) * 100, 2)
            : 0.0;

        return [
            'scenario' => $appliedScenario,
            'headline' => $headline,
            'purchase_price_pence' => $priceInPence,
            'total_tax_pence' => $totalTaxPence,
            'effective_rate' => $effectiveRate,
            'groups' => $groups,
            'notes' => $notes,
        ];
    }

    private function calculateBandBreakdown(
        int $priceInPence,
        array $bands,
        bool $isSurcharge,
        int $rateOverrideBps = 0
    ): array {
        $rows = [];
        $totalTaxPence = 0;

        foreach ($bands as $band) {
            $lowerLimit = $this->toPence($band['up_to_previous']);
            $upperLimit = $band['up_to'] === null ? null : $this->toPence($band['up_to']);

            if ($priceInPence <= $lowerLimit) {
                continue;
            }

            $taxableAmount = $upperLimit === null
                ? $priceInPence - $lowerLimit
                : min($priceInPence, $upperLimit) - $lowerLimit;

            if ($taxableAmount <= 0) {
                continue;
            }

            $rateBps = $rateOverrideBps ?: $band['rate_bps'];
            $taxDue = (int) round(($taxableAmount * $rateBps) / 10000);
            $totalTaxPence += $taxDue;

            $rows[] = [
                'label' => $this->bandLabel($band['up_to_previous'], $band['up_to'], $isSurcharge),
                'taxable_amount_pence' => $taxableAmount,
                'rate_bps' => $rateBps,
                'tax_due_pence' => $taxDue,
            ];
        }

        return [
            'rows' => $rows,
            'total_tax_pence' => $totalTaxPence,
        ];
    }

    private function bandLabel(int $fromPounds, ?int $toPounds, bool $isSurcharge): string
    {
        $prefix = $isSurcharge ? 'Extra 5% on ' : '';

        if ($fromPounds === 0 && $toPounds !== null) {
            return $prefix.'the portion up to '.$this->formatWholePounds($toPounds);
        }

        if ($toPounds === null) {
            return $prefix.'the portion above '.$this->formatWholePounds($fromPounds);
        }

        return $prefix.'the portion from '.$this->formatWholePounds($fromPounds + 1).' to '.$this->formatWholePounds($toPounds);
    }

    private function toPence(int|float|string $amount): int
    {
        return (int) round(((float) $amount) * 100);
    }

    private function formatPounds(int|float $amount): string
    {
        return '£'.number_format((float) $amount, 2);
    }

    private function formatWholePounds(int $amount): string
    {
        return '£'.number_format($amount, 0);
    }
}
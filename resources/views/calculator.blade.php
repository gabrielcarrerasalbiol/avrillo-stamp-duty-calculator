<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Stamp Duty Calculator</title>
    <style>
        :root {
            color-scheme: light;
            --bg: #eef2ea;
            --panel: #ffffff;
            --ink: #1f2527;
            --muted: #5f6664;
            --line: #dce1db;
            --brand: #86a91b;
            --brand-dark: #6f8f11;
            --brand-soft: #e4edd0;
            --success: #527711;
            --shadow: 0 20px 40px rgba(26, 36, 31, 0.12);
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: "Segoe UI", Arial, Helvetica, sans-serif;
            color: var(--ink);
            background: var(--bg);
            min-height: 100vh;
        }

        .page {
            max-width: 1240px;
            margin: 0 auto;
            padding: 0 20px 56px;
        }

        .hero {
            display: block;
            margin-bottom: 22px;
        }

        .card {
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 22px;
            box-shadow: var(--shadow);
        }

        .intro {
            padding: 44px 36px;
            min-height: 430px;
            border-radius: 0 0 18px 18px;
            border: 0;
            position: relative;
            overflow: hidden;
            color: #fff;
            background-image: linear-gradient(rgba(28, 43, 20, 0.56), rgba(28, 43, 20, 0.56)), url('https://images.unsplash.com/photo-1600585152915-d208bec867a1?auto=format&fit=crop&w=1800&q=80');
            background-size: cover;
            background-position: center;
        }

        .hero-logo {
            display: inline-block;
            width: 170px;
            max-width: 52%;
            margin-bottom: 14px;
        }

        .hero-logo img {
            width: 100%;
            height: auto;
            display: block;
        }

        .eyebrow {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 12px;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            background: rgba(255, 255, 255, 0.16);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.28);
        }

        h1 {
            margin: 18px 0 12px;
            font-size: clamp(2rem, 4vw, 3.3rem);
            line-height: 1.05;
            font-weight: 700;
            max-width: 640px;
        }

        .intro p,
        .hint,
        .summary-copy,
        .field-help,
        .note,
        .error-copy {
            color: var(--muted);
        }

        .intro .summary-copy,
        .intro .hint {
            color: rgba(255, 255, 255, 0.92);
            max-width: 700px;
        }

        .intro-grid {
            display: grid;
            gap: 14px;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            margin-top: 30px;
            max-width: 880px;
        }

        .intro-grid div {
            padding: 16px;
            border-radius: 14px;
            background: rgba(134, 169, 27, 0.82);
            border: 1px solid rgba(255, 255, 255, 0.25);
        }

        .intro-grid strong {
            display: block;
            margin-bottom: 6px;
            color: #fff;
        }

        .form-card {
            padding: 24px;
            position: relative;
            z-index: 1;
        }

        .result-card {
            padding: 24px;
        }

        .workspace {
            display: grid;
            gap: 20px;
            align-items: start;
        }

        .result-placeholder {
            border: 1px dashed #b7c49a;
            border-radius: 16px;
            padding: 26px;
            background: #f8fbf2;
        }

        .result-placeholder h2 {
            margin: 0 0 8px;
            color: #2f3b1f;
            font-size: 1.4rem;
        }

        .result-placeholder p {
            margin: 0;
            color: var(--muted);
        }

        .stack {
            display: grid;
            gap: 18px;
        }

        label {
            display: block;
            font-size: 0.98rem;
            font-weight: 700;
            margin-bottom: 8px;
        }

        input[type="text"],
        select {
            width: 100%;
            padding: 14px 16px;
            border-radius: 16px;
            border: 1px solid var(--line);
            background: #fff;
            color: var(--ink);
            font-size: 1rem;
        }

        .radio-grid {
            display: grid;
            gap: 12px;
        }

        .choice,
        .checkbox {
            display: flex;
            gap: 12px;
            align-items: flex-start;
            padding: 14px 16px;
            border: 1px solid var(--line);
            border-radius: 18px;
            background: #fff;
        }

        .choice input,
        .checkbox input {
            margin-top: 4px;
        }

        .choice strong,
        .checkbox strong {
            display: block;
            margin-bottom: 4px;
        }

        .field-error {
            margin-top: 8px;
            color: #7f341d;
            font-size: 0.95rem;
        }

        .error-summary {
            border: 1px solid rgba(111, 143, 17, 0.32);
            background: #f3f7ea;
            border-radius: 18px;
            padding: 16px 18px;
        }

        .error-summary ul {
            margin: 10px 0 0;
            padding-left: 20px;
            color: #7f341d;
        }

        .error-summary li + li {
            margin-top: 6px;
        }

        .warning-box {
            margin-top: 10px;
            border: 1px solid rgba(146, 110, 25, 0.35);
            background: #fff9e8;
            color: #6a4a05;
            border-radius: 12px;
            padding: 10px 12px;
            font-size: 0.95rem;
            display: none;
        }

        .warning-box.is-visible {
            display: block;
        }

        button {
            appearance: none;
            border: 0;
            border-radius: 999px;
            padding: 14px 20px;
            background: linear-gradient(135deg, var(--brand), var(--brand-dark));
            color: #fff;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: transform 220ms ease, box-shadow 220ms ease;
        }

        button:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(134, 169, 27, 0.38);
        }

        button:active {
            transform: translateY(1px);
            box-shadow: none;
        }

        .result-card {
            opacity: 0;
            transform: translateY(18px) scale(0.995);
            transition: opacity 420ms ease, transform 520ms cubic-bezier(0.2, 0.8, 0.2, 1);
        }

        .result-card.is-visible {
            opacity: 1;
            transform: translateY(0) scale(1);
        }

        .reveal-item {
            opacity: 0;
            transform: translateY(18px);
            transition: opacity 480ms ease, transform 520ms cubic-bezier(0.2, 0.8, 0.2, 1);
        }

        .reveal-item.is-visible {
            opacity: 1;
            transform: translateY(0);
        }

        .result-top {
            display: flex;
            justify-content: space-between;
            gap: 18px;
            align-items: end;
            border-bottom: 1px solid var(--line);
            padding-bottom: 18px;
            margin-bottom: 20px;
        }

        .result-total {
            font-size: clamp(2rem, 4vw, 3.4rem);
            line-height: 1;
            margin: 8px 0 0;
            color: #243018;
        }

        .result-metric {
            min-width: 180px;
            padding: 16px;
            border-radius: 18px;
            background: var(--brand-soft);
            color: var(--success);
            text-align: center;
        }

        .group {
            border: 1px solid var(--line);
            border-radius: 16px;
            overflow: hidden;
            margin-top: 18px;
        }

        .group header {
            padding: 16px 18px;
            background: #f6f8f2;
            display: flex;
            justify-content: space-between;
            gap: 12px;
            align-items: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            text-align: left;
            padding: 12px 14px;
            border-top: 1px solid var(--line);
            vertical-align: top;
        }

        th {
            font-size: 0.82rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--muted);
        }

        .notes {
            margin-top: 18px;
            padding: 18px;
            border-radius: 18px;
            background: #f4f7ec;
            border: 1px solid var(--line);
        }

        .notes ul {
            margin: 12px 0 0;
            padding-left: 18px;
        }

        .notes li + li {
            margin-top: 8px;
        }

        @media (max-width: 900px) {
            .page {
                padding-inline: 14px;
            }

            .intro {
                min-height: 360px;
                padding: 34px 22px;
                border-radius: 0 0 14px 14px;
            }

            h1 {
                font-size: clamp(1.7rem, 7vw, 2.5rem);
            }

            .intro-grid {
                grid-template-columns: 1fr;
            }

            .form-card {
                padding: 18px;
            }

            .result-top {
                flex-direction: column;
                align-items: start;
            }
        }

        @media (min-width: 980px) {
            .workspace.has-result {
                grid-template-columns: 1.02fr 0.98fr;
            }
        }

        @media (max-width: 680px) {
            .card,
            .group {
                border-radius: 14px;
            }

            th:nth-child(2),
            td:nth-child(2) {
                display: none;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            button,
            .result-card,
            .reveal-item {
                transition: none;
                transform: none;
            }
        }
    </style>
</head>
<body>
    @php
        $form = $submitted ?? [
            'purchase_price' => old('purchase_price'),
            'buyer_type' => old('buyer_type', 'standard'),
            'owns_additional_property' => old('owns_additional_property'),
        ];

        $money = static fn (int $pence): string => '£'.number_format($pence / 100, 2);
    @endphp

    <div class="page">
        <section class="hero">
            <article class="card intro">
                <a class="hero-logo" href="{{ route('sdlt.index') }}" aria-label="Avrillo Conveyancing home">
                    <img src="{{ asset('images/avrillo_white.svg') }}" alt="Avrillo Conveyancing">
                </a>
                <span class="eyebrow">England residential SDLT</span>
                <h1>Stamp duty without the tax-code wording.</h1>
                <p class="summary-copy">Enter the purchase details, and this calculator shows the tax due, how each slice of the price is charged, and the overall rate across the full purchase price.</p>

                <div class="intro-grid">
                    <div>
                        <strong>Standard purchase</strong>
                        <span class="hint">Current residential bands from April 2025.</span>
                    </div>
                    <div>
                        <strong>First-time buyer relief</strong>
                        <span class="hint">Applied automatically when the price is £500,000 or less.</span>
                    </div>
                    <div>
                        <strong>Additional property</strong>
                        <span class="hint">Adds the higher-rate surcharge on top of the base calculation.</span>
                    </div>
                </div>
            </article>
        </section>

        <section class="workspace {{ isset($result) ? 'has-result' : '' }}">
            <section class="card form-card">
                <form method="POST" action="{{ route('sdlt.calculate') }}" class="stack" novalidate>
                    @csrf

                    @if ($errors->any())
                        <div class="error-summary" role="alert" aria-live="assertive">
                            <strong>There is a problem with the information entered.</strong>
                            <p class="error-copy">Fix the fields below and calculate again.</p>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div>
                        <label for="purchase_price">Property price</label>
                        <input
                            id="purchase_price"
                            name="purchase_price"
                            type="text"
                            inputmode="decimal"
                            pattern="^\d+(?:[\.,]\d{0,2})?$"
                            autocomplete="off"
                            spellcheck="false"
                            value="{{ $form['purchase_price'] }}"
                            placeholder="e.g. 425000"
                            aria-describedby="purchase-price-help"
                        >
                        <div id="purchase-price-help" class="field-help">Enter the agreed purchase price in pounds.</div>
                        @error('purchase_price')
                            <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label>Buyer status</label>
                        <div class="radio-grid">
                            <label class="choice">
                                <input id="buyer-type-standard" type="radio" name="buyer_type" value="standard" {{ ($form['buyer_type'] ?? 'standard') === 'standard' ? 'checked' : '' }}>
                                <span>
                                    <strong>At least one buyer has owned a home before</strong>
                                    <span class="field-help">Use the normal residential rates.</span>
                                </span>
                            </label>

                            <label class="choice">
                                <input id="buyer-type-ftb" type="radio" name="buyer_type" value="first_time_buyer" {{ ($form['buyer_type'] ?? '') === 'first_time_buyer' ? 'checked' : '' }}>
                                <span>
                                    <strong>All buyers are first-time buyers</strong>
                                    <span class="field-help">If the price is above £500,000, the relief does not apply.</span>
                                </span>
                            </label>
                        </div>
                        <div class="warning-box {{ (($form['buyer_type'] ?? '') === 'first_time_buyer' && !empty($form['owns_additional_property'])) ? 'is-visible' : '' }}" id="buyer-warning" role="status" aria-live="polite">
                            First-time buyer relief cannot be used with an additional property purchase. If you continue, the result will use standard residential rates plus the additional property surcharge.
                        </div>
                        @error('buyer_type')
                            <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="checkbox">
                            <input type="hidden" name="owns_additional_property" value="0">
                            <input id="owns-additional-property" type="checkbox" name="owns_additional_property" value="1" {{ !empty($form['owns_additional_property']) ? 'checked' : '' }}>
                            <span>
                                <strong>This purchase is subject to the higher rates for additional properties</strong>
                                <span class="field-help">Tick this only if the buyer will still own another residential property worth £40,000 or more after completion and is not replacing their main home.</span>
                            </span>
                        </label>
                        @error('owns_additional_property')
                            <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit">Calculate stamp duty</button>
                </form>
            </section>

            @isset($result)
                <section class="card result-card" id="result-card" data-animate-result="true">
                    <div class="result-top reveal-item">
                        <div>
                            <span class="eyebrow">Calculation result</span>
                            <h2 class="result-total" data-final-total-pence="{{ $result['total_tax_pence'] }}">{{ $money($result['total_tax_pence']) }}</h2>
                            <p class="summary-copy">{{ $result['headline'] }} on a purchase price of {{ $money($result['purchase_price_pence']) }}.</p>
                        </div>

                        <div class="result-metric reveal-item">
                            <div>Effective rate</div>
                            <strong data-final-effective-rate="{{ number_format($result['effective_rate'], 2, '.', '') }}">{{ number_format($result['effective_rate'], 2) }}%</strong>
                        </div>
                    </div>

                    @foreach ($result['groups'] as $group)
                        <section class="group reveal-item">
                            <header>
                                <strong>{{ $group['title'] }}</strong>
                                <span>{{ $money($group['total_tax_pence']) }}</span>
                            </header>
                            <table>
                                <thead>
                                    <tr>
                                        <th>How this slice is charged</th>
                                        <th>Portion of the price</th>
                                        <th>Rate</th>
                                        <th>Tax due</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($group['rows'] as $row)
                                        <tr>
                                            <td>{{ $row['label'] }}</td>
                                            <td>{{ $money($row['taxable_amount_pence']) }}</td>
                                            <td>{{ number_format($row['rate_bps'] / 100, 0) }}%</td>
                                            <td>{{ $money($row['tax_due_pence']) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </section>
                    @endforeach

                    @if (!empty($result['notes']))
                        <section class="notes reveal-item">
                            <strong>Notes</strong>
                            <ul>
                                @foreach ($result['notes'] as $note)
                                    <li class="note">{{ $note }}</li>
                                @endforeach
                            </ul>
                        </section>
                    @endif
                </section>
            @else
                <section class="card result-card-placeholder">
                    <div class="result-placeholder">
                        <h2>Result preview</h2>
                        <p>After you calculate, the SDLT total, effective rate, and full band-by-band breakdown will appear here.</p>
                    </div>
                </section>
            @endisset
        </section>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var resultCard = document.getElementById('result-card');
            var buyerTypeFtb = document.getElementById('buyer-type-ftb');
            var additionalPropertyCheckbox = document.getElementById('owns-additional-property');
            var buyerWarning = document.getElementById('buyer-warning');
            var purchasePriceInput = document.getElementById('purchase_price');

            var formatWithThousandsSeparator = function (value) {
                var cleaned = value.replace(/[^\d]/g, '');

                if (!cleaned) {
                    return '';
                }

                return cleaned.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            };

            if (purchasePriceInput) {
                purchasePriceInput.addEventListener('input', function () {
                    purchasePriceInput.value = formatWithThousandsSeparator(purchasePriceInput.value);
                });

                purchasePriceInput.addEventListener('paste', function (event) {
                    event.preventDefault();
                    var pasted = (event.clipboardData || window.clipboardData).getData('text');
                    purchasePriceInput.value = formatWithThousandsSeparator(pasted);
                });

                purchasePriceInput.value = formatWithThousandsSeparator(purchasePriceInput.value);
            }

            var syncBuyerWarning = function () {
                if (!buyerTypeFtb || !additionalPropertyCheckbox || !buyerWarning) {
                    return;
                }

                if (buyerTypeFtb.checked && additionalPropertyCheckbox.checked) {
                    buyerWarning.classList.add('is-visible');
                } else {
                    buyerWarning.classList.remove('is-visible');
                }
            };

            if (buyerTypeFtb && additionalPropertyCheckbox && buyerWarning) {
                buyerTypeFtb.addEventListener('change', syncBuyerWarning);

                document.querySelectorAll('input[name="buyer_type"]').forEach(function (input) {
                    input.addEventListener('change', syncBuyerWarning);
                });

                additionalPropertyCheckbox.addEventListener('change', syncBuyerWarning);
                syncBuyerWarning();
            }

            if (!resultCard) {
                return;
            }

            var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            var totalNode = resultCard.querySelector('[data-final-total-pence]');
            var effectiveNode = resultCard.querySelector('[data-final-effective-rate]');

            var toMoney = function (pence) {
                var pounds = pence / 100;
                return new Intl.NumberFormat('en-GB', {
                    style: 'currency',
                    currency: 'GBP',
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }).format(pounds);
            };

            var animateNumber = function (from, to, duration, onStep, onDone) {
                var start = performance.now();

                var tick = function (now) {
                    var elapsed = now - start;
                    var progress = Math.min(elapsed / duration, 1);
                    var eased = 1 - Math.pow(1 - progress, 3);
                    var value = from + ((to - from) * eased);

                    onStep(value, progress);

                    if (progress < 1) {
                        requestAnimationFrame(tick);
                    } else if (onDone) {
                        onDone();
                    }
                };

                requestAnimationFrame(tick);
            };

            requestAnimationFrame(function () {
                resultCard.classList.add('is-visible');

                var revealItems = resultCard.querySelectorAll('.reveal-item');

                revealItems.forEach(function (item, index) {
                    var delay = reduceMotion ? 0 : (index * 90);
                    setTimeout(function () {
                        item.classList.add('is-visible');
                    }, delay);
                });

                if (!totalNode || !effectiveNode) {
                    return;
                }

                var totalPence = Number(totalNode.getAttribute('data-final-total-pence'));
                var effectiveRate = Number(effectiveNode.getAttribute('data-final-effective-rate'));

                if (reduceMotion) {
                    totalNode.textContent = toMoney(totalPence);
                    effectiveNode.textContent = effectiveRate.toFixed(2) + '%';
                    return;
                }

                animateNumber(0, totalPence, 900, function (value) {
                    totalNode.textContent = toMoney(Math.round(value));
                });

                animateNumber(0, effectiveRate, 900, function (value) {
                    effectiveNode.textContent = value.toFixed(2) + '%';
                });
            });
        });
    </script>
</body>
</html>
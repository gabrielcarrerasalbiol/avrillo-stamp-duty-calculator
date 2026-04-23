PROCESS

How long this actually took

I have not recorded a reliable wall-clock total outside this build session, so I would not trust a made-up number here. If this repository is being submitted for the exercise, this section should be replaced with the real elapsed time from the person submitting it.
Honestly one hour and a half...

How I used AI tooling

I used GitHub Copilot to scaffold the Laravel application, set up the Docker environment, draft the SDLT service, and generate the first pass of the tests and interface. I then verified and corrected the outputs against the brief and HMRC guidance.

One specific example I rewrote: an early breakdown label approach produced range text like `£125,000.01`, which is technically derived from pence conversion but clearly wrong for client-facing SDLT bands. I replaced that with whole-pound threshold labels such as `£125,001 to £250,000`.

For branding and UI replication, I used AI to iterate quickly from a neutral layout to an AVRillo-style direction based on the provided visual reference. Direct scraping of the live site was blocked by anti-bot protection, so I treated the supplied screenshot as the source of truth and implemented:

- a hero-first page with strong visual banner treatment
- green-forward palette and CTA styling aligned to the reference look
- a compact responsive workspace with form left and results right on desktop, stacked on mobile
- subtle result animations (reveal + count-up) with reduced-motion fallback

I also used AI to refactor this safely without touching calculation logic, then re-ran feature tests after each major UI pass.

How I verified the maths

I used HMRC's current GOV.UK guidance pages for residential rates, first-time buyer relief, and higher rates for additional properties as the source for the config values.

I checked the implementation against:

- HMRC's published standard-rate worked example for a £295,000 purchase, which gives £4,750.
- HMRC's published first-time buyer example for a £500,000 purchase, which gives £10,000.
- HMRC's published higher-rates example for a £300,000 additional property purchase, which gives £20,000.
- Automated unit tests for standard, first-time buyer, additional property, threshold boundaries, surcharge threshold behaviour, and the first-time buyer price cap fallback.
- Feature tests for calculator page load, valid submission response, validation error handling, and the first-time-buyer-plus-additional-property edge case (returns a result with explanatory note).
- Additional regression check for `GET /calculate` to avoid a 405 and redirect users to the main calculator page.
- Test runs on host and in Docker to verify local developer workflow is consistent.

Latest run results in this workspace:

- `php artisan test`
- `docker compose exec app php artisan test`
- Outcome: **13 passed, 0 failed** (35 assertions) in both host and Docker runs.
- Includes: full unit and feature suite, calculator route/regression checks, submission validation checks, and SDLT calculation coverage.



How it works:

The form submits with POST to the calculate route:
resources/views/calculator.blade.php
The POST endpoint is defined here:
routes/web.php
The calculation runs in Laravel on the server and returns a rendered response (not client-side math, not AJAX).
Routing hardening:

A GET request to /calculate is redirected back to / to avoid a 405 page:
routes/web.php
So for that judgement-call line in the plan:

Chosen approach: form POST (server-side)
Status: completed and aligned with preferred direction.


What I'd do with another hour

- Add a small results summary sentence that explicitly tells the user why a particular rate set was chosen.
- Add more HTTP tests around decimals, empty input, and re-submitting the form after validation errors.
- Add a lightweight route/API response for calculator results so the same service can back an AJAX UI later.
- Move inline CSS into compiled assets and split style tokens/components for maintainability.
- Add visual regression snapshots for the branded layout across desktop and mobile breakpoints.


Nice to have (beyond brief)

- Add a small security-hardening middleware for production headers (CSP, nosniff, frame/permissions/referrer policies), while keeping local development flexible.
- Add route-level rate limiting on the calculate endpoint to reduce abuse.
- Move the inline JavaScript/CSS into versioned asset files so CSP can be tightened cleanly.
- Add CI checks for `composer audit`, test runs, and basic static analysis before merge.
- Add an optional purchase-date field with date-effective rate tables for parity with broader public calculators.
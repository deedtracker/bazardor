# BazarDor — Pricing Engine v2 Architecture

**Goal:** Replace the hardcoded, one-size-fits-all district multiplier logic with a data-driven engine that correctly handles nationally-flat products like rice and beef, and removes silent failure points in the current code.

This is a schema + logic redesign, not a UI redesign — the admin still does one thing day-to-day: pick a product, enter a base price, hit save.

This document describes the requirements and intended behavior only. Your developer should review the existing database structure and PHP codebase and design the actual schema, queries, and implementation — the specifics of table design, column types, and query structure are theirs to decide based on what's already there.

---

## 1. Core problem with v1

Every product currently goes through the same district-tier randomization, regardless of whether that product's real-world price actually varies by district. Rice, beef, mutton, sugar, edible oil, salt — these are nationally uniform in practice (govt-monitored, long shelf life, or high substitutability keeps arbitrage out). Vegetables and fish genuinely do vary by district (farming hubs, river hubs). v1 has no way to express that difference except by editing PHP source.

Secondary problems in v1 worth fixing at the same time:
- District tiers and category bounds are hardcoded PHP arrays — any tuning requires a code deploy.
- Tier lookup is a string match against Bengali city names — a typo or new city silently falls back to a default tier with no warning.
- There's no record of which rule set produced a given historical price, so past data can't be audited or reproduced if bounds change later.
- There's no way to manually override a single district's price for a single day (e.g. a real flood spike in Sylhet) without it being overwritten by the next tier calculation.

## 2. Data model changes needed

The developer should review the current schema and decide the concrete implementation, but functionally the system needs to be able to represent:

- **A pricing mode per product** — each product needs to be classifiable as either "flat" (same price applies to every district, no randomization) or "tiered" (current district-variance behavior applies). This should be settable from the product edit screen and default to "tiered" so nothing changes for existing products until it's explicitly changed.
- **District-to-tier assignment as data, not code** — every city needs an explicit tier assignment stored somewhere queryable, so there's no silent fallback when a city is missing or misspelled. Ideally this surfaces a warning/log if a city has no assignment, instead of quietly defaulting.
- **Category-to-tier price bounds as data, not code** — the min/max adjustment range per tier per category needs to live somewhere editable without a deploy, replacing the current hardcoded bounds arrays.
- **Per-category tier overrides** — the ability to say "this city acts as a different tier for this specific category" (the current override logic), stored as data rather than an `if` block.
- **Provenance on price rows** — each city-level price should be able to indicate whether it was engine-estimated or came from a real reported source, and whether it's been manually corrected (and therefore shouldn't be silently overwritten by the next engine run).

Nothing about the existing `daily_prices`, `city_prices`, `products`, `cities`, or `categories` tables needs to be dropped — this only adds structure around them, and your developer is best placed to decide exactly how given what's already there.

## 3. Pricing engine logic — intended behavior

When a base price is saved for a product:

- If the product is in **flat** mode, the same price should be applied to every district, with no randomized adjustment. (Whether to allow a tiny amount of natural-looking noise, e.g. ±1 taka, or none at all, is a call worth making explicitly — see open questions below.)
- If the product is in **tiered** mode, the behavior should match what's already live today: look up each city's tier (checking for a category-specific override first), look up the adjustment range for that tier and category, apply a random adjustment within that range, and round to the nearest 5 as it does now. The only thing that should change is that tiers and bounds are read from data instead of hardcoded arrays — the actual math and rounding/floor behavior stays the same.
- Whichever mode produced a price, the row should be marked as engine-estimated.
- If a city has no tier assignment, that should be logged/flagged rather than silently defaulting, so it gets caught and fixed instead of quietly producing wrong numbers indefinitely.

Everything about how a price is computed for tiered products should stay behaviorally identical to what's live now — only where the rules are stored changes.

## 4. Admin panel changes

1. **Product edit screen:** add a way to set a product's pricing mode (flat vs. tiered), defaulting to tiered so existing products are unaffected until explicitly changed.
2. **A way to edit tier assignments and category bounds without a deploy** — worth having, but can come after the core engine change ships; the current hardcoded values work fine as a starting point.

Day-to-day price entry shouldn't change at all — pick product, enter base price, save.

## 5. Prediction system

Not touched by this redesign conceptually — the prediction logic reads from price history the same way regardless of whether a day's row was flat or tiered. Worth having your developer confirm the prediction model doesn't assume tiered variance always exists — if it uses city-level spread as an input signal, that signal legitimately becomes zero for flat products, which is correct behavior, not a bug to fix.

## 6. Migration plan (safe order of operations)

1. Add whatever new structure is needed plus the pricing-mode setting. No behavior change yet — every existing product should default to tiered mode.
2. Populate the tier and bounds data from the current hardcoded PHP values. Diff a day of output against v1 to confirm identical results before cutting over.
3. Swap the price-saving logic to read from the new data instead of the arrays. Should be behaviorally identical to v1 for tiered products at this point.
4. Flip specific products to flat mode — rice, beef, mutton, sugar, salt, soybean oil (finalize the full list first). This is the first step that actually changes displayed prices.

## 7. Open questions for your developer to confirm with you before building

- Full list of products that should be flat (you named rice, beef — worth finalizing the complete list up front).
- Whether flat products should get zero variance or a tiny noise band.
- Whether the estimated/reported distinction should ever surface publicly (a small "estimated" note) or stay internal for now.

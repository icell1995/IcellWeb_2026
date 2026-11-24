# AI Guidelines for IcellWeb

## Scope and workflow

- Target **Laravel 12 and PHP 8.5.x**. Check `composer.json`, `composer.lock`, and the current code before following examples. Treat audits and plans in `docs/upgrade/` as historical context.
- Start with Git changes and the feature entry point. Use `rg` to locate routes, callers, and tests. Read relevant sections, exclude `vendor/` and legacy backup files (`*.bak*`, `*.bakk`, `*.baks`) from searches and edits, and limit tool output.
- Make the smallest change that fulfills the request. Reuse existing patterns and services. Preserve HTTP/API contracts, authorization, schema, and historical data. Keep unrelated refactors, dependencies, and formatting out of scope.
- Keep updates concise. Report the outcome, checks run, and remaining blockers. Consult additional references when they inform a decision.

## PHP and Laravel style

- Use appropriate parameter and return types, explicit nullability, and clear names in new code. Use `casts(): array`, `Attribute::make()`, and typed Eloquent relationships where appropriate. Keep the existing Kernel and provider structure and modernize one feature at a time.
- Use Form Requests for complex or repeated validation. Controllers handle request flow and responses. Keep reusable business logic in the appropriate component. Use transactions when database writes must succeed together.
- Use `env()` only in `config/`. Read configuration via `config()` and rotating values from existing runtime storage. Whenever adding or referencing `config('services.*')` keys, verify they are defined in `config/services.php` (and `.env.example`). Use `ESignatureTokenService` for electronic signature tokens.
- Check `AppModeHelper::isApiEnabled()` when modifying external API integrations (IRSMS, Pusiknas, EMP, WhatsApp) so non-production gating is intentional and required read/detail endpoints are not silently blocked.
- Follow Laravel Pint conventions in touched files. Keep route names unique and check callers before changing names, URLs, or HTTP methods.

## Blade and frontend conventions

- For long forms (documents, personnel, etc.), follow the inline validation pattern (`.is-invalid`, `.frontend-error`, Select2/radio support, real-time cleanup on input/change) with `scrollToFirstError()` and no blocking SweetAlert error-list modal.
- Always provide an empty/error fallback state in Blade views backed by external APIs so failed or empty responses never render a blank page.

## Security and data integrity

- Enforce authentication and authorization on the server through the applicable policies, gates, or middleware. Check `RegionalScopeService` and `DocumentAccessMiddleware` before adding work-unit or document access rules. Test allowed and denied access.
- Validate input, including keys in nested arrays. Map writable fields explicitly. Set ownership, internal status, and audit fields on the server. Bind SQL parameters and allowlist dynamic column names and sort directions.
- Use Blade escaping by default. Keep OTPs, tokens, credentials, and sensitive data out of logs and unintended output. Preserve CSRF protection, rate limiting, and upload validation where applicable.

## Queries and N+1

- Keep queries out of Blade, accessors, and loops when data can be fetched in batches. Use `with()`, `withCount()`, `withExists()`, or batch queries as appropriate. Include relationship keys when selecting specific columns.
- Paginate in the database, including for DataTables. Use Query Builder or parameterized SQL for aggregations or legacy schemas when appropriate. Preserve work-unit filters and soft-delete behavior.
- Keep lazy-loading detection enabled outside production. For list or query-heavy changes, test multiple records and verify that query count does not grow per record. Record query count or latency before and after optimization.

## Verification

- Run relevant tests on PHP 8.5, for example `php artisan test --filter=TestName`. Add regression coverage for new behavior or authorization changes that existing tests miss.
- Test PostgreSQL-specific queries and migrations against a test database that supports the project's schemas. Test route or configuration caching in isolation when changing those areas. Use fakes or test endpoints for external integrations.

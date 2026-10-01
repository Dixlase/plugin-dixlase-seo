# Changelog

All notable changes to the Dixlase SEO plugin are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this plugin follows Semantic Versioning.

## [0.1.1] — 2026-10-01

### Added

- **Default social title** setting (`default_title`) in admin SEO base settings.
  It feeds `og:title`, is translatable per locale through DixlaseMultilingual
  (registered on the `dixlase-seo:settings` singleton type), and falls back to
  the application name when left empty, so existing installs keep their current
  output.
- `twitter:title` is now emitted explicitly instead of relying on X's `og:title`
  fallback — the same reasoning already applied to `twitter:image`.

### Fixed

- `og:title` was hardcoded to `config('app.name')`, the identical value emitted
  as `og:site_name`. Every page of every site therefore shared one title, a
  share card carried the site name twice, and the only way to change it was to
  rename the application. There was no admin field, no per-locale value, and no
  way to differ between locales.

## [0.1.0] — 2026-10-01
Initial release. Requires Dixlase `^0.1.0` (Plugin API `^0.1`), PHP `>= 8.3`.

### Added

- Meta tag, OGP, and JSON-LD injection into front-end responses via the `web`
  middleware group.
- `sitemap.xml` and `robots.txt` front routes.
- Admin settings — base SEO, external integrations (Google Analytics **GA4**
  measurement ID, Google Search Console verification), and sitemap options.
- Google Analytics (GA4) tag emission **gated on cookie consent** (`analytics`
  category): no tag is emitted until consent is granted, via Core's
  `ConsentStateProviderInterface`.
- Implements `App\Contracts\CspPolicyProvider` to add `googletagmanager.com` /
  `google-analytics.com` to the CSP dynamically when GA is enabled.
- Provides the `seo` capability.

# Changelog

All notable changes to the Dixlase SEO plugin are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this plugin follows Semantic Versioning.

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

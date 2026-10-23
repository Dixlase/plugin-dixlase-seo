# Changelog

All notable changes to the Dixlase SEO plugin are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this plugin follows Semantic Versioning.

## [0.2.5] — 2026-10-24

### Changed

- Comments name the core mechanism an integration goes through instead of the extensions that
  provide it, and a test no longer uses such an extension's slug as its fixture (#60, fixes #58).
  No behaviour changed.

### Development

- The deploy workflow ships a published release only, never a push to `main`, and both the release
  build and the deploy refuse a tree that does not match the signature manifest (#63, #64; part of
  Dixlase/dixlase-core#520).
- Between releases `main` carries a development version, such as `0.2.5-dev` (#61).
- Bump `vite` from 8.3.1 to 8.3.3, a build-time dependency that is not shipped to sites (#62).

## [0.2.4] — 2026-10-18

### Changed

- The README and this changelog no longer name plugins that are not distributed publicly. Each integration is
  described by the core mechanism it goes through (#57, fixes #56). No behaviour changed.

## [0.2.3] — 2026-10-17

### Added

- A page can carry its own OGP image per language. A content plugin opts in by declaring
  `seo_ogp_media_id` (type `media`) in its `multilingual_content`; each locale URL then
  shares the image translated for that locale, and a language with no translation keeps
  the stored image. A translated image whose media item has been deleted falls through to
  the stored one rather than emitting a broken URL. Plugins that do not declare the field
  are unchanged (#54, fixes #53).

## [0.2.2] — 2026-10-16

### Development

- A self-audit test replays the admin plugin audit's own checks: it fails when a
  permission declared in `plugin.json` does not match the code, or when a view breaks
  the CSP strict mode (#52, fixes #51). No runtime code changed.

## [0.2.1] — 2026-10-12

### Fixed

- The first item under "SEO Management" in the admin menu now reads "SEO Base Settings"
  instead of "Base Settings", so it says what it configures (#48, fixes #47).

### Development

- A smoke test opens every admin GET route of the plugin as the admin and the editor
  role and checks that none returns a server error (#44, fixes #43).
- Tests that only configure return values use test stubs instead of mocks, which clears
  nine PHPUnit notices (#46, fixes #45).

## [0.2.0] — 2026-10-11

### Added

- Each page now emits its own OGP image, title and type, instead of falling back
  to the site-wide values for every page (#42).

### Fixed

- Re-signed. The signature no longer matched the tree, so sites reported the plugin
  as `signature_invalid` and deducted from its health score. Changes had reached
  `main` through pull-request merges, which do not run the local signing hook.

### Changed

- Built front-end assets are no longer tracked in git; the release build produces
  them (#36).
- CI and dependency maintenance: `source-map-js` 1.2.1 → 1.2.2 (#39), the dead
  `COMPOSER_AUTH` block removed (#38), and the checkout token no longer passed as
  a secret (#40).

## [0.1.2] — 2026-10-01

### Changed

- Build tooling: `vite` 5 → 8.3.1, with `esbuild` and `postcss` 8.5.28 updated
  alongside (#31). This clears the Dependabot advisories for those packages, all of
  which affect only the development server and the asset build — nothing in them is
  shipped to sites. The prebuilt assets in the release ZIP are produced by the same
  build as before; only their hashed file names change.

## [0.1.1] — 2026-10-01

### Added

- **Default social title** setting (`default_title`) in admin SEO base settings.
  It feeds `og:title`, is translatable per locale (registered on the
  `dixlase-seo:settings` singleton type), and falls back to the application name when left empty, so existing installs keep their current
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

# Dixlase SEO

For Japanese, see [README.ja.md](./README.ja.md).

SEO optimization for Dixlase: site-wide meta tags, OGP and X (Twitter) Card output, Organization JSON-LD, an automatically generated XML sitemap, robots.txt management, and Google Analytics / Search Console wiring — all from one admin panel. Other plugins that declare the `seo-meta` capability (such as DixlasePages) plug in so their content can carry per-entity meta descriptions and OGP images.

## Features

- **Meta tags** — Site-wide title separator and default meta description, injected into front-end responses automatically.
- **OGP / X (Twitter) Card** — Default OGP image and type, plus X Card type (summary / summary_large_image) and site account.
- **Organization JSON-LD** — Structured data (name, logo, URL) for richer search results.
- **XML sitemap** — Auto-generated sitemap with configurable change frequency and priority, served at `/sitemap.xml`.
- **robots.txt** — Auto or manual mode, served at `/robots.txt`.
- **External services** — Google Analytics measurement ID and Google Search Console site verification.
- **Plugin integrations** — Per-plugin toggle for SEO-meta features (meta description, OGP image) on every plugin that supports them.
- **Role-based permission** — Admin-only access to the SEO settings screens.

## Installation

Open the admin panel under **Dashboard → Plugins**, find this plugin, then download and enable it. The plugin's tables are created automatically on enable.

## Usage

Once enabled, **SEO Management** appears in the admin sidebar with Base Settings, Sitemap, External Services, and Plugin Integrations screens.

The sitemap and robots.txt are published automatically at `/sitemap.xml` and `/robots.txt`. Default meta tags, OGP, and JSON-LD are injected into front-end pages out of the box; plugins that declare `seo-meta` can override them per page.

## Capabilities

This plugin declares the following capability in `plugin.json`:

- **`seo`** — Marks this plugin as the site's SEO provider. It consumes the `seo-meta` capability exposed by other plugins (e.g. DixlasePages) to read and write per-entity meta descriptions and OGP images, and surfaces them under **Plugin Integrations**.

## License

Dixlase SEO is distributed under a **dual license**:

- **Open Source License**: [GNU General Public License v3](./LICENSE)
- **Commercial License**: A separate commercial license is planned for use cases where GPL v3 compliance is not feasible. **It is not yet available** — only a placeholder of the eventual terms is present in [LICENSE-COMMERCIAL](./LICENSE-COMMERCIAL). For availability timing or other questions, contact **info@dixlase.org**.

A short overview of how these files fit together is in [NOTICE](./NOTICE) ([日本語](./NOTICE.ja)).

## Contributing

We do not yet accept external code Pull Requests.  
They will open once we have assessed core API stability and how the project operates after the initial release, and prepared a Contributor License Agreement (CLA) that has passed legal review.  
Once the CLA is finalized, contributions will fall under the [Dixlase Copyright Policy](https://github.com/Dixlase/dixlase-core/blob/main/COPYRIGHT-POLICY.md) and the Dixlase CLA (see CONTRIBUTING.md).  
Bug reports and proposals via Issues are welcome.  
For feature proposals, please take a look at [the Dixlase philosophy](https://dixlase.org/en/philosophy) — and consider whether the feature belongs in the core or could work as a plugin. It helps us align on direction.

---

© 2026 exc-D inc. and Dixlase contributors

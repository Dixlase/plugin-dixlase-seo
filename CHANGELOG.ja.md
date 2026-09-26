# 変更履歴

Dixlase SEO プラグインの主要な変更はすべてこのファイルに記録します。

フォーマットは [Keep a Changelog](https://keepachangelog.com/en/1.1.0/) に準拠し、
本プラグインはセマンティックバージョニングに従います。

## [0.1.0] — 2026-10-01
初回リリース。Dixlase `^0.1.0`（Plugin API `^0.1`）、PHP `>= 8.3` が必要です。

### 追加

- `web` ミドルウェアグループを介した、フロントエンド応答へのメタタグ・OGP・
  JSON-LD の注入。
- `sitemap.xml` と `robots.txt` のフロントルート。
- 管理設定 — 基本 SEO、外部連携（Google Analytics **GA4** 測定 ID、Google
  Search Console 確認）、サイトマップオプション。
- Cookie 同意（`analytics` カテゴリ）を条件とする Google Analytics（GA4）タグ
  出力: コアの `ConsentStateProviderInterface` を介し、同意が得られるまでタグを
  出力しません。
- `App\Contracts\CspPolicyProvider` を実装し、GA 有効時に `googletagmanager.com`
  / `google-analytics.com` を CSP に動的に追加。
- `seo` capability を提供。

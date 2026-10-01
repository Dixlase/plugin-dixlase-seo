# 変更履歴

Dixlase SEO プラグインの主要な変更はすべてこのファイルに記録します。

フォーマットは [Keep a Changelog](https://keepachangelog.com/en/1.1.0/) に準拠し、
本プラグインはセマンティックバージョニングに従います。

## [0.1.2] — 2026-10-01

### 変更

- ビルドツール: `vite` を 5 から 8.3.1 に更新し、`esbuild` と `postcss`(8.5.28)も
  あわせて更新(#31)。これらのパッケージに出ていた Dependabot の勧告を解消した。
  どれも開発サーバと画面用ファイルのビルドだけに関わるもので、サイトに配布される
  ものには含まれない。リリース ZIP のビルド済みファイルは同じビルドで作られ、
  ハッシュ付きのファイル名だけが変わる。

## [0.1.1] — 2026-10-01

### 追加

- 管理画面の SEO 基本設定に**デフォルトのSNSタイトル**（`default_title`）を追加。
  `og:title` に使用され、DixlaseMultilingual 経由でロケールごとに翻訳可能
  （`dixlase-seo:settings` シングルトン型に登録）。空の場合はアプリケーション名に
  フォールバックするため、既存インストールの出力は変わりません。
- `twitter:title` を明示的に出力。X の `og:title` フォールバックに依存しない形に
  しました（`twitter:image` と同じ方針）。

### 修正

- `og:title` が `config('app.name')` 固定で、`og:site_name` と同一の値だった問題を
  修正。全サイトの全ページが同一のタイトルを共有し、シェアカードにサイト名が2回
  表示され、変更するにはアプリケーション名を変えるしかありませんでした。管理画面の
  入力欄もロケール別の値も存在しませんでした。

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

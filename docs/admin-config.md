# DixlaseSEO 管理画面設定ガイド

## 概要

**DixlaseSEO** は、Dixlase サイトの SEO / OGP / サイトマップ / 外部サービス連携を統合管理する公式プラグインです。管理画面は 4 つの設定タブに分かれています。

## アクセス方法

管理画面 → **SEO 管理**

タブ構成：

| タブ | 内容 |
| --- | --- |
| **基本設定 (base)** | 全ページ共通のメタタグ / OGP / Organization 情報 |
| **サイトマップ (sitemap)** | XML サイトマップと robots.txt の生成制御 |
| **外部連携 (external)** | Google Analytics / Search Console 認証等 |
| **プラグイン連携 (integrations)** | 各プラグイン（Pages / ブログ等）で SEO メタを持たせるかの有効化 |

---

## 1. 基本設定タブ

### 一般 (General)

| 項目 | 説明 | 例 |
| --- | --- | --- |
| **タイトルセパレーター** | 各ページタイトルとサイト名を連結する記号 | `｜` / `-` / `–` |
| **デフォルト説明** | 個別ページで meta description が指定されていないときのフォールバック | 「Dixlase は…（120〜160 字程度）」 |

### OGP

| 項目 | 説明 |
| --- | --- |
| **デフォルト OGP 画像** | 個別ページで OGP 画像が未指定のときの共通画像。1200×630px 推奨 |
| **Twitter Card タイプ** | `summary`（小さい画像）または `summary_large_image`（大きい画像）を選択 |
| **Twitter サイトアカウント** | `@yourbrand` 形式でサイト運営アカウントを指定 |

### Organization（構造化データ）

| 項目 | 説明 |
| --- | --- |
| **組織名** | schema.org の `Organization` `name` に出力される |
| **組織ロゴ** | schema.org 用のロゴ画像（正方形推奨） |

これらは各ページの `<head>` に JSON-LD として自動出力され、Google の Knowledge Graph 認識を助けます。

---

## 2. サイトマップタブ

### XML サイトマップ

| 項目 | 説明 | デフォルト |
| --- | --- | --- |
| **サイトマップ有効化** | `/sitemap.xml` の生成 ON/OFF | ON 推奨 |
| **更新頻度 (changefreq)** | `daily` / `weekly` / `monthly` 等の目安 | weekly |
| **優先度 (priority)** | 0.0〜1.0 の目安 | 0.5 |

サイトマップは Pages / ブログ / メニュー等の公開コンテンツから自動生成されます。有効化後は Google Search Console にサイトマップ URL を登録するのが定石です。

### robots.txt

サイトマップ設定と連動して `/robots.txt` にサイトマップ URL のヒントが自動記載されます。

---

## 3. 外部連携タブ

### Google Analytics

| 項目 | 説明 | 例 |
| --- | --- | --- |
| **Google Analytics ID** | GA4 の測定 ID | `G-XXXXXXXXXX` |

DixlaseCookie を導入している場合、**訪問者が「分析」カテゴリを許可したときのみ** トラッカーが動く仕組みになります（同意ベース計測）。

### Google Search Console

| 項目 | 説明 |
| --- | --- |
| **サイト確認メタタグ** | Search Console の HTML タグ確認方式で提供される `content` 値を入力 |

これで全ページの `<head>` にメタタグが挿入され、Search Console のサイト所有権確認が完了します。

---

## 4. プラグイン連携タブ

### 各プラグインで SEO メタを持たせる

Pages / ブログ等のプラグインごとに、**個別のコンテンツで SEO メタタグを上書き可能にするか**を切り替えます。

- **有効化**: 該当プラグインの編集画面に「SEO メタ」セクションが追加され、ページごとに `title` / `description` / OGP 画像を個別指定可能に
- **無効化**: 基本設定のデフォルト値のみ適用

### 孤児（orphan）メタの整理

プラグインを無効化・削除したとき、そのプラグイン由来の SEO メタが DB に残ることがあります。「孤児のクリーンアップ」を実行すると、対応するコンテンツが失われた SEO メタを一括削除できます。

---

## 運用の流れ（推奨）

1. **基本設定** でサイト共通のデフォルトを入れる（description / OGP 画像 / Twitter card / Organization）
2. **サイトマップ** を有効化 → Search Console に登録
3. **外部連携** で Google Analytics ID / Search Console 確認タグを入力
4. **プラグイン連携** で Pages 等の個別ページに SEO メタを持たせる設定を有効化
5. 各ページ編集画面で個別メタを設定（必要なページのみ）

## 他プラグインとの連携

- **DixlaseCookie**: 分析トラッカーの同意ベース制御。Cookie 導入時、GA は同意後のみ動作
- **DixlasePages**: プラグイン連携タブで有効化すると、各ページに SEO メタ入力欄が追加
- **DixlaseMultilingual**: 多言語ページごとに `title` / `description` を言語別指定可能

## トラブルシューティング

| 症状 | 原因 | 対処 |
| --- | --- | --- |
| Google Analytics が反応しない | Cookie 同意プラグイン導入下で「分析」が拒否されている | Cookie トリガーから同意を確認 / 拒否時のフォールバック実装は本プラグイン範囲外 |
| Search Console でサイト所有権確認できない | メタタグ入力が空、または誤った値 | 「サイト確認メタタグ」欄に正しい `content` 値を再入力 |
| サイトマップに古い URL が残る | プラグイン無効化後の orphan メタ | プラグイン連携タブから「孤児クリーンアップ」実行 |
| OGP 画像が表示されない | 画像 URL が絶対 URL ではない、または画像サイズが規格外 | 1200×630 px の絶対 URL 画像を指定 |

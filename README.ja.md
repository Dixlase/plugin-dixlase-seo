# Dixlase SEO

For English, see [README.md](./README.md).

Dixlase 用の SEO 最適化プラグイン。

- サイト全体のメタタグ、OGP、X (Twitter) Card の出力
- Organization JSON-LD による構造化データ
- 変更頻度・優先度を設定できる XML サイトマップの自動生成
- robots.txt の管理(自動 / 手動)
- Google Analytics / Search Console との連携
- `seo-meta` capability を宣言したプラグイン(DixlasePages など)との連携

## 機能

- **メタタグ** — サイト共通のタイトル区切り文字とデフォルトのメタディスクリプションを、フロントのレスポンスへ自動挿入。
- **OGP / X (Twitter) Card** — デフォルト OGP 画像・タイプに加え、X Card タイプ(summary / summary_large_image)とサイトアカウントを設定可能。
- **Organization JSON-LD** — 名称・ロゴ・URL の構造化データでリッチな検索結果に対応。
- **XML サイトマップ** — 変更頻度・優先度を設定可能なサイトマップを自動生成し、`/sitemap.xml` で配信。
- **robots.txt** — 自動 / 手動モードを切り替え、`/robots.txt` で配信。
- **外部サービス連携** — Google Analytics 測定 ID と Google Search Console のサイト確認。
- **プラグイン連携** — SEO メタ機能(メタディスクリプション / OGP 画像)に対応する各プラグインごとに有効・無効を切り替え。
- **ロール権限** — SEO 設定画面は管理者のみアクセス可能。

## インストール

管理画面の **ダッシュボード → プラグイン** から本プラグインを検索し、ダウンロード → 有効化します。有効化すると本プラグイン用のテーブルが自動で作成されます。

## 使い方

有効化すると管理画面のサイドバーに **SEO管理** が追加され、基本設定 / サイトマップ / 外部サービス連携 / プラグイン連携の各画面で設定できます。

サイトマップと robots.txt は `/sitemap.xml`・`/robots.txt` で自動的に配信されます。デフォルトのメタタグ・OGP・JSON-LD は標準でフロントのページに挿入され、`seo-meta` を宣言したプラグインはページごとに上書きできます。

## Capabilities

本プラグインは `plugin.json` で以下の capability を宣言しています。

- **`seo`** — 本プラグインをサイトの SEO プロバイダとして登録します。他プラグイン(DixlasePages など)が公開する `seo-meta` capability を消費して、エンティティごとのメタディスクリプションや OGP 画像を読み書きし、**プラグイン連携** 画面に表示します。

## ライセンス

Dixlase SEO は **デュアルライセンス** で配布されています。

- **オープンソースライセンス**: [GNU General Public License v3](./LICENSE)
- **商用ライセンス**: GPL v3 の遵守が現実的でないユースケース向けに、別途商用ライセンスの提供を予定しています。

**現時点では商用ライセンスはまだ提供しておりません。**  
(雛形のみ [LICENSE-COMMERCIAL](./LICENSE-COMMERCIAL) に Draft として置いています)。  
提供開始時期や条件に関するお問い合わせは **info@dixlase.org** までご連絡ください。

各ファイルの関係概要は [NOTICE.ja](./NOTICE.ja)([English](./NOTICE))にあります。

## コントリビューションについて

CLA (Contributor License Agreement) のレビュー中のため、現在 Pull Request を受け付けていません。  
CLA 確定後に受付を開始し、その時点から [Dixlase Copyright Policy](https://github.com/Dixlase/dixlase-core/blob/main/COPYRIGHT-POLICY.md) と Dixlase CLA(詳細は CONTRIBUTING.md)の対象となります。  
それまでも Issue での不具合報告・機能提案は歓迎しています。

---

(C) exc-D inc.

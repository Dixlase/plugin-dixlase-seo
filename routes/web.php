<?php

/**
 * This file is part of DixlaseSEO.
 *
 * Copyright (C) 2026
 * https://example.com
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| DixlaseSEO Web Routes
|--------------------------------------------------------------------------
|
| フロントエンド用のルート定義
|
| 注意: このファイルは自動的に以下のミドルウェアが適用されます
| - web: セッション、CSRF保護
| - front.ip: フロントエンドIPアドレス制限
|
| セキュリティに関する注意:
| - front.ip ミドルウェアでIPアドレスフィルタリングを実施します
| - IPアドレスフィルタリングを実施しないとセキュリティリスクが高まります
|
*/

// フロントエンド用のルート
// 例: Route::get('/dixlase-seo', [Controller::class, 'index'])->name('dixlase-seo.index');
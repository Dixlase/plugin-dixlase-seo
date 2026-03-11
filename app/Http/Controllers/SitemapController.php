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

namespace Plugins\DixlaseSEO\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Plugins\DixlaseSEO\App\Models\DixlaseSeoSetting;
use Plugins\DixlaseSEO\App\Services\SitemapGenerator;

/**
 * XMLサイトマップのコントローラー
 */
class SitemapController extends Controller
{
    public function __construct(
        private readonly SitemapGenerator $sitemapGenerator,
    ) {}

    /**
     * XMLサイトマップを出力する
     */
    public function index(): Response
    {
        $enabled = DixlaseSeoSetting::getValue('sitemap_enabled', '1');

        if ($enabled !== '1' && $enabled !== true) {
            abort(404);
        }

        $xml = $this->sitemapGenerator->generate();

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=utf-8',
        ]);
    }
}

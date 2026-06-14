<?php

/**
 * This file is part of Dixlase SEO.
 *
 * Copyright (C) 2026 exc-D inc.
 * https://exc-d.com
 *
 * Dixlase SEO is dual-licensed. You may use this file under either:
 *
 *   (a) the GNU General Public License version 3 or later, as published
 *       by the Free Software Foundation; or
 *
 *   (b) a commercial license agreement obtained from exc-D inc.
 *
 * Unless you have entered into a commercial license agreement, this
 * file is governed by the GPL terms below.
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

namespace Plugins\DixlaseSEO\App\Services;

/**
 * Request-scoped holder for the meta description of the content currently
 * being rendered.
 *
 * A front-end view (e.g. DixlasePages / DixlaseLegal `front/page.blade.php`)
 * resolves its own meta description for the current locale and hands the
 * final string to SEO via the `dls_seo_set_page_meta()` helper.
 * SeoMetaGenerator then emits it when building the `<head>` instead of the
 * site-wide default.
 *
 * Registered as a container singleton, so it lives for the duration of one
 * request and is empty by default (site-wide default description applies).
 */
class SeoContext
{
    private ?string $description = null;

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }
}

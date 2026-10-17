<?php

/**
 * This file is part of Dixlase SEO.
 *
 * Copyright (C) 2026 exc-D inc. and Dixlase contributors
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
 * Request-scoped holder for the SEO meta of the content currently being
 * rendered.
 *
 * A content plugin's front-end view (e.g. DixlasePages'
 * `front/page.blade.php`) hands its own values to SEO via the
 * `dls_seo_set_page_meta()` helper.
 * SeoMetaGenerator then emits them when building the `<head>` instead of the
 * site-wide defaults.
 *
 * Registered as a container singleton, so it lives for the duration of one
 * request and is empty by default (the site-wide settings apply).
 *
 * Every slot is optional and independent: a page that sets only a description
 * still gets the site-wide image, title and OGP type.
 */
class SeoContext
{
    private ?string $description = null;

    private ?string $title = null;

    private ?int $localizedOgpMediaId = null;

    private ?int $ogpMediaId = null;

    private ?int $fallbackOgpMediaId = null;

    private ?string $ogType = null;

    private ?string $entityPluginSlug = null;

    private ?string $entityId = null;

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * The rendered page's own title, already resolved for the request locale
     * by the caller. Takes precedence over the site-wide `default_title`.
     */
    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Media id made for the current locale: the page's `seo_ogp_media_id`
     * translation. Tried before `ogpMediaId`, so each language URL can
     * share its own image.
     */
    public function setLocalizedOgpMediaId(?int $localizedOgpMediaId): void
    {
        $this->localizedOgpMediaId = $localizedOgpMediaId;
    }

    public function getLocalizedOgpMediaId(): ?int
    {
        return $this->localizedOgpMediaId;
    }

    /**
     * Media id the editor picked for this entity, read from the SEO meta
     * table. The first candidate for `og:image` / `twitter:image`.
     */
    public function setOgpMediaId(?int $ogpMediaId): void
    {
        $this->ogpMediaId = $ogpMediaId;
    }

    public function getOgpMediaId(): ?int
    {
        return $this->ogpMediaId;
    }

    /**
     * Media id to use when the entity has no SEO image of its own — for
     * instance a blog post's featured image. Tried after `ogpMediaId` and
     * before the site-wide `default_ogp_image`.
     */
    public function setFallbackOgpMediaId(?int $fallbackOgpMediaId): void
    {
        $this->fallbackOgpMediaId = $fallbackOgpMediaId;
    }

    public function getFallbackOgpMediaId(): ?int
    {
        return $this->fallbackOgpMediaId;
    }

    /**
     * OGP type for this page (e.g. `article` for a blog post). Takes
     * precedence over the site-wide `ogp_type` setting.
     */
    public function setOgType(?string $ogType): void
    {
        $this->ogType = $ogType;
    }

    public function getOgType(): ?string
    {
        return $this->ogType;
    }

    /**
     * Record which entity is being rendered, so anything building the
     * `<head>` can identify it without deriving it from the route again.
     */
    public function setEntity(?string $pluginSlug, ?string $entityId): void
    {
        $this->entityPluginSlug = $pluginSlug;
        $this->entityId = $entityId;
    }

    public function getEntityPluginSlug(): ?string
    {
        return $this->entityPluginSlug;
    }

    public function getEntityId(): ?string
    {
        return $this->entityId;
    }
}

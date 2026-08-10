<?php

/**
 * This file is part of Dixlase SEO.
 *
 * Copyright (C) 2026 exc-D inc. and Dixlase contributors
 * https://exc-d.com
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

namespace Plugins\DixlaseSEO\Tests\Feature\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Plugins\DixlaseSEO\App\Models\DixlaseSeoSetting;
use Plugins\DixlaseSEO\App\Providers\DixlaseSEOServiceProvider;
use Tests\TestCase;

/**
 * Reading settings must degrade to the default when the plugin's own table is
 * absent, instead of throwing.
 *
 * A plugin can be enabled while its schema is not there: right after install,
 * when a migration failed, or mid-rollback. In that window the unguarded query
 * did not merely break SEO — DixlaseSEOServiceProvider::getCspDirectives() is
 * called by the core CSP policy registry from the ContentSecurityPolicy
 * middleware, so the PDOException surfaced on EVERY request and returned 500
 * for the whole site. InjectSeoMetaTags reaches the same method on every
 * request too.
 *
 * The sibling DixlaseSeoSettingTest covers the happy path; these cases need
 * the opposite starting point, which has to be constructed — see
 * dropSettingsTable().
 */
class DixlaseSeoSettingMissingTableTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Reproduce the "plugin enabled, schema absent" window.
     *
     * The suite bootstrap creates the plugin's tables, so the state has to be
     * made rather than found. Dropping is done per-test rather than in setUp()
     * so the last case can still exercise normal operation: the migration
     * ledger already records this migration as run, so a dropped table cannot
     * simply be migrated back.
     *
     * Safe here — the test connection is an in-memory SQLite database rebuilt
     * for every test.
     */
    private function dropSettingsTable(): void
    {
        Schema::dropIfExists((new DixlaseSeoSetting())->getTable());

        $this->assertFalse(
            Schema::hasTable((new DixlaseSeoSetting())->getTable()),
            'This case only means anything while the settings table is absent.'
        );
    }

    public function test_get_value_returns_null_instead_of_throwing(): void
    {
        $this->dropSettingsTable();

        $this->assertNull(DixlaseSeoSetting::getValue('google_analytics_id'));
    }

    public function test_get_value_returns_the_supplied_default(): void
    {
        $this->dropSettingsTable();

        $this->assertSame(
            'fallback',
            DixlaseSeoSetting::getValue('google_analytics_id', 'fallback')
        );
    }

    public function test_csp_directives_are_empty_rather_than_fatal(): void
    {
        $this->dropSettingsTable();

        // The exact path that took the site down: CspPolicyRegistry calls this
        // for every request through the ContentSecurityPolicy middleware.
        $provider = new DixlaseSEOServiceProvider($this->app);

        $this->assertSame([], $provider->getCspDirectives());
    }

    public function test_values_are_read_normally_once_the_table_exists(): void
    {
        // The guard must not cost us the feature: with the schema in place the
        // model reads and writes exactly as before. Deliberately does NOT drop
        // the table, so it also pins down that the probe is re-evaluated per
        // call rather than cached from another test's negative result.
        DixlaseSeoSetting::setValue('google_analytics_id', 'G-TEST123');

        $this->assertSame('G-TEST123', DixlaseSeoSetting::getValue('google_analytics_id'));
        $this->assertNotSame([], (new DixlaseSEOServiceProvider($this->app))->getCspDirectives());
    }
}

<?php

namespace App\PerDocs\ModelAsPage;

use SilverStripe\Admin\ModelAdmin;

class RegionAdmin extends ModelAdmin
{
    private static $url_segment = 'region-admin';

    private static $menu_title = 'Region Admin (per docs)';

    private static $menu_priority = -2;

    private static $managed_models = [
        Region::class,
    ];
}

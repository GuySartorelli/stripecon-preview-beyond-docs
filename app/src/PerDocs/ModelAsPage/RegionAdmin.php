<?php

namespace App\PerDocs\ModelAsPage;

use SilverStripe\Admin\ModelAdmin;

class RegionAdmin extends ModelAdmin
{
    private static $url_segment = 'region-admin';

    private static $menu_title = 'Region Admin';

    private static $managed_models = [
        Region::class,
    ];
}

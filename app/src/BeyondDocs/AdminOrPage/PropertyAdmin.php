<?php

namespace App\BeyondDocs\AdminOrPage;

use SilverStripe\Admin\ModelAdmin;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\View\SSViewer;

class PropertyAdmin extends ModelAdmin
{
    private static $url_segment = 'property-admin';

    private static $menu_title = 'Property Admin (beyond docs)';

    private static $menu_priority = -3;

    private static $url_handlers = [
        '$ModelClass/cmsPreview/$ID' => 'cmsPreview',
    ];

    private static $allowed_actions = [
        'cmsPreview',
    ];

    private static $managed_models = [
        Property::class,
    ];

    public function cmsPreview(): DBHTMLText
    {
        $id = $this->urlParams['ID'];
        $obj = $this->modelClass::get_by_id($id);
        if (!$obj || !$obj->exists()) {
            // NOTE: This will redirect you to the edit form for the "Not Found" page if one exists.
            $this->httpError(404);
        }

        // Allow use of front-end themes temporarily.
        $oldThemes = SSViewer::get_themes();
        SSViewer::set_themes(SSViewer::config()->get('themes'));
        $preview = $obj->forTemplate();

        // Make sure to set back to backend themes.
        SSViewer::set_themes($oldThemes);

        return $preview;
    }
}

<?php

namespace App\PerDocs\CustomAdmin;

use SilverStripe\Admin\ModelAdmin;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\View\Requirements;
use SilverStripe\View\SSViewer;

class ProductAdmin extends ModelAdmin
{
    private static $url_segment = 'product-admin';

    private static $menu_title = 'Product Admin (per docs)';

    private static $menu_priority = -1;

    private static $url_handlers = [
        '$ModelClass/cmsPreview/$ID' => 'cmsPreview',
    ];

    private static $allowed_actions = [
        'cmsPreview',
    ];

    private static $managed_models = [
        Product::class,
    ];

    public function cmsPreview(): DBHTMLText
    {
        $id = $this->urlParams['ID'];
        $obj = $this->modelClass::get_by_id($id);
        if (!$obj || !$obj->exists()) {
            // NOTE: This will redirect you to the edit form for the "Not Found" page if one exists.
            $this->httpError(404);
        }

        // Include use of a front-end theme temporarily and clear any CMS requirements.
        $oldThemes = SSViewer::get_themes();
        SSViewer::set_themes(SSViewer::config()->get('themes'));
        Requirements::clear();

        // Render the preview content
        // Note that if your template is an include and relies on global css or js, you should
        // use the Requirements API here to include those
        $preview = $obj->forTemplate();

        // Make sure to set back to backend themes and restore CMS requirements.
        SSViewer::set_themes($oldThemes);
        Requirements::restore();

        return $preview;
    }
}

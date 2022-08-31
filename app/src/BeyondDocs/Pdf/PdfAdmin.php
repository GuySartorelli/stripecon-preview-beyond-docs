<?php

namespace App\BeyondDocs\Pdf;

use SilverStripe\Admin\ModelAdmin;

class PdfAdmin extends ModelAdmin
{
    private static $url_segment = 'pdf-admin';

    private static $menu_title = 'Pdf Admin';

    private static $url_handlers = [
        '$ModelClass/cmsPreview/$ID' => 'cmsPreview',
    ];

    private static $allowed_actions = [
        'cmsPreview',
    ];

    private static $managed_models = [
        PdfTemplate::class,
        ConcretePdf::class,
    ];

    public function cmsPreview(): void
    {
        $id = $this->urlParams['ID'];
        $obj = $this->modelClass::get_by_id($id);
        if (!$obj || !$obj->exists()) {
            // NOTE: This will redirect you to the edit form for the "Not Found" page if one exists.
            $this->httpError(404);
        }

        $obj->sendToBrowser();
    }
}

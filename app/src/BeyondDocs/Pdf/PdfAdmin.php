<?php

namespace App\BeyondDocs\Pdf;

use SilverStripe\Admin\ModelAdmin;
use SilverStripe\View\Requirements;

class PdfAdmin extends ModelAdmin
{
    private static $url_segment = 'pdf-admin';

    private static $menu_title = 'Pdf Admin';

    private static $url_handlers = [
        '$ModelClass/cmsPreview/$ID/$ExtraID' => 'cmsPreview',
    ];

    private static $allowed_actions = [
        'cmsPreview',
    ];

    private static $managed_models = [
        PdfTemplate::class,
        ConcretePdf::class,
        Client::class,
    ];

    protected function init()
    {
        parent::init();
        if ($this->modelClass === Client::class) {
            // You might actually want this in LeftAndMain.extra_requirements_javascript in case you use clients elsewhere.
            Requirements::javascript('app/src/BeyondDocs/Pdf/javascript/client-preview-pdf.js');
        }
    }

    public function cmsPreview()
    {
        $id = $this->urlParams['ID'] ?? 0;
        $obj = $this->modelClass::get_by_id($id);
        if (!$obj || !$obj->exists()) {
            // NOTE: This will redirect you to the edit form for the "Not Found" page if one exists.
            $this->httpError(404);
        }

        if ($obj instanceof Client) {
            return $obj->previewPDF($this->urlParams['ExtraID'] ?? 0);
        } else {
            $obj->sendToBrowser();
        }
    }
}

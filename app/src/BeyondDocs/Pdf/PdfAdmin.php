<?php

namespace App\BeyondDocs\Pdf;

use GuySartorelli\GridFieldPreview\PreviewableModelAdminExtension;
use SilverStripe\Admin\ModelAdmin;
use SilverStripe\View\Requirements;

class PdfAdmin extends ModelAdmin
{
    private static string $url_segment = 'pdf-admin';

    private static string $menu_title = 'Pdf Admin (beyond docs)';

    private static $menu_priority = -4;

    private static array $url_handlers = [
        '$ModelClass/cmsPreview/$ID/$ExtraID' => 'cmsPreview',
    ];

    private static array $allowed_actions = [
        'cmsPreview',
    ];

    private static array $managed_models = [
        PdfTemplate::class,
        ConcretePdf::class,
        Client::class,
    ];

    /**
     * NOTE: This is added through PreviewableModelAdminExtension
     */
    private static array $gridfield_previewable_classes = [
        PdfTemplate::class,
    ];

    private static array $extensions = [
        PreviewableModelAdminExtension::class,
    ];

    protected function init()
    {
        parent::init();
        if ($this->modelClass === Client::class) {
            // You might actually want this in LeftAndMain.extra_requirements_javascript in case you use clients elsewhere.
            // I'm including it here primarily to keep all the related demo code together as much as I can.
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

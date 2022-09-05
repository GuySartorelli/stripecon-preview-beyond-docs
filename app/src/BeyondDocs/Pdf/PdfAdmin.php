<?php

namespace App\BeyondDocs\Pdf;

use SilverStripe\Admin\LeftAndMain;
use SilverStripe\Admin\ModelAdmin;
use SilverStripe\CMS\Controllers\SilverStripeNavigator;
use SilverStripe\Forms\GridField\GridFieldConfig;
use SilverStripe\Forms\LiteralField;
use SilverStripe\ORM\CMSPreviewable;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\View\Requirements;
use SilverStripe\View\SSViewer;

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
            // I'm including it here primarily to keep all the related demo code together as much as I can.
            Requirements::javascript('app/src/BeyondDocs/Pdf/javascript/client-preview-pdf.js');
        }

        // You'd do this through a separate css file normally.
        // I'm doing this here to keep all the related demo code together as much as I can.
        if ($this->modelClass === PdfTemplate::class) {
            Requirements::customCSS(<<<'css'
                .PdfAdmin.cms-content .cms-content-view {
                    display: flex;
                    flex-direction: column;
                    padding-bottom: 0;
                }
                css
            );
        }
    }

    public function getEditForm($id = null, $fields = null)
    {
        $form = parent::getEditForm($id, $fields);
        // Include preview controls for the PdfTemplate GridField.
        if ($this->modelClass === PdfTemplate::class) {
            // Mark as previewable.
            $form->addExtraClass('cms-previewable');
            // Add preview controls.
            $navField = LiteralField::create('SilverStripeNavigator', $this->getDefaultSilverStripeNavigator());
            $navField->setAllowHTML(true);
            $form->Fields()->push($navField);
        }
        return $form;
    }

    protected function getGridFieldConfig(): GridFieldConfig
    {
        $config = parent::getGridFieldConfig();
        // Allow previewing items directly from the PdfTemplate GridField.
        if ($this->modelClass === PdfTemplate::class) {
            $config->addComponent(GridFieldPreviewButton::create());
        }
        return $config;
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

    /**
     * Gets a SilverStripeNavigator that isn't tied to a specific record.
     */
    private function getDefaultSilverStripeNavigator(): DBHTMLText
    {
        $record = $this->modelClass::singleton();
        $navigator = SilverStripeNavigator::create($record);
        $templates = SSViewer::get_templates_by_class(static::class, '_SilverStripeNavigator', LeftAndMain::class);
        $renderWith = SSViewer::chooseTemplate($templates);
        return $navigator->renderWith($renderWith);
    }
}

<?php

namespace App\PerDocs\CustomAdmin;

use SilverStripe\Control\Controller;
use SilverStripe\ORM\CMSPreviewable;
use SilverStripe\ORM\DataObject;

class Product extends DataObject implements CMSPreviewable
{
    private static $table_name = 'App_CustomAdmin_Product';

    private static $show_unversioned_preview_link = true;

    private static $db = [
        'Name' => 'Varchar',
        'ProductCode' => 'Varchar',
        'Price' => 'Currency'
    ];

    private static $summary_fields = [
        'Name',
        'ProductCode',
        'Price',
    ];

    public function PreviewLink($action = null)
    {
        if (!$this->isInDB()) {
            return null;
        }
        // The ProductAdmin will handle the cmsPreview path and output some previewable content for this object.
        $admin = ProductAdmin::singleton();
        return Controller::join_links(
            $admin->Link(str_replace('\\', '-', $this->ClassName)),
            'cmsPreview',
            $this->ID
        );
    }

    public function CMSEditLink()
    {
        // This isn't strictly necessary in this specific example,
        // but it makes sense to implement a useful value since this method is mandatory.
        $admin = ProductAdmin::singleton();
        $sanitisedClassname = str_replace('\\', '-', $this->ClassName);
        return Controller::join_links(
            $admin->Link($sanitisedClassname),
            'EditForm/field/',
            $sanitisedClassname,
            'item',
            $this->ID
        );
    }

    public function getMimeType()
    {
        return 'text/html';
    }

    public function forTemplate()
    {
        // If the template for this class is not an "Include" template, use the appropriate type here e.g. "Layout".
        return $this->renderWith(['type' => 'Includes', self::class]);
    }
}

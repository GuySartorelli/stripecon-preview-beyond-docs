<?php

namespace App\PerDocs\CustomAdmin;

use SilverStripe\Control\Controller;
use SilverStripe\ORM\CMSPreviewable;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\FieldType\DBHTMLText;

class Product extends DataObject implements CMSPreviewable
{
    private static string $table_name = 'App_CustomAdmin_Product';

    private static bool $show_unversioned_preview_link = true;

    private static array $db = [
        'Name' => 'Varchar',
        'ProductCode' => 'Varchar',
        'Price' => 'Currency'
    ];

    private static array $summary_fields = [
        'Name',
        'ProductCode',
        'Price',
    ];

    public function PreviewLink($action = null): ?string
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

    public function CMSEditLink(): string
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

    public function getMimeType(): string
    {
        return 'text/html';
    }

    public function forTemplate(): DBHTMLText
    {
        // If the template for this class is not an "Include" template, use the appropriate type here e.g. "Layout".
        return $this->renderWith(['type' => 'Includes', self::class]);
    }
}

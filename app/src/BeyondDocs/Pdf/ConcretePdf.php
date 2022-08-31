<?php

namespace App\BeyondDocs\Pdf;

use SilverStripe\Forms\DropdownField;
use SilverStripe\ORM\CMSPreviewable;
use SilverStripe\ORM\DataObject;

class ConcretePdf extends DataObject implements CMSPreviewable
{
    use Pdf, PdfPreviewable;

    private static string $table_name = 'App_Pdf_ConcretePdf';

    private static $db = [
        'NameField' => 'Varchar',
        'Content' => 'HTMLText',
    ];

    private static $has_one = [
        'Template' => PdfTemplate::class,
        'DataItem' => DataObject::class,
    ];

    private static array $summary_fields = [
        'Name',
        'Content.Summary' => 'Content',
    ];

    private static bool $show_unversioned_preview_link = true;

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->replaceField(
            'NameField',
            DropdownField::create('NameField', $this->fieldLabel('NameField'), [])
                ->setDescription('The DB field that represents the name of the data item.')
        );
        $fields->insertBefore('NameField', $fields->dataFieldByName('TemplateID'));
        $fields->insertAfter(
            'TemplateID',
            DropdownField::create('DataItemID', $this->fieldLabel('DataItem'), [])
                ->setDescription('The data item which will be used to substitute variables for the PDF.')
        );

        return $fields;
    }

    public function getName(): string
    {
        $dataItem = $this->DataItem();
        $nameField = $this->NameField;
        if (!$dataItem->exists() || !$nameField) {
            return 'NOT CONFIGURED';
        }
        return $dataItem->$nameField;
    }

    public function getPdfOptions(): array
    {
        $template = $this->Template();
        return $template->exists() ? $template->getPdfOptions() : [];
    }
}

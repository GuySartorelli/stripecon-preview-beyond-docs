<?php

namespace App\BeyondDocs\Pdf;

use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\ORM\CMSPreviewable;
use SilverStripe\ORM\DataObject;

/**
 * Note: This example is not complete - it's just a skeleton to show how a concrete PDF DataObject might related to the PdfTemplates.
 * You would include some javascript so that when the template is selected, it updates the DataItemID and NameField dropdowns.
 * It would also update the Content field with the content from the template.
 * You could then edit the content if the template wasn't 100% exactly what you wanted for this situation.
 * An onAfterWrite() would save or print or email the resultant PDF, and set the CMS fields to readonly. At that stage it will have
 * been sent off the the client and exists in the DB for recording purposes only.
 * Probably you'd want it to be versioned, and have that on onAfterPublish() instead.... you get the idea.
 */
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

        $fields->insertBefore('TemplateID', LiteralField::create('notice', '<p class="alert alert-danger">This is not fully implemented - see comment in code.</p>'));

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

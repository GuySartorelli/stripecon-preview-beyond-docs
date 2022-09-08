<?php

namespace App\BeyondDocs\Pdf;

use SilverStripe\Forms\CompositeValidator;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\ORM\CMSPreviewable;
use SilverStripe\ORM\DataObject;
use SilverStripe\Security\Member;

class PdfTemplate extends DataObject implements CMSPreviewable
{
    use Pdf, PdfPreviewable;

    private static string $table_name = 'App_Pdf_PdfTemplate';

    private static $db = [
        'Name' => 'Varchar(255)',
        'ShowTitleInHeader' => 'Boolean',
        'ShowPageNumbers' => 'Boolean',
        // You'd want to be a little fancy here and use a colour picker module instead of just a text field.
        // You'd probably also only want colour on the splash page which maybe you'd have a separate template
        // for or something.
        'BGColor' => 'Varchar',
        'FGColor' => 'Varchar',
        'Content' => 'HTMLText',
        'DataSourceClass' => 'Varchar',
    ];

    private static array $summary_fields = [
        'Name',
        'Content.Summary' => 'Content',
    ];

    private static bool $show_unversioned_preview_link = true;

    private static array $data_source_classes = [
        Member::class => 'Member',
        Client::class => 'Client',
    ];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->insertBefore(
            'Content',
                DropdownField::create(
                'DataSourceClass',
                $this->fieldLabel('DataSourceClass'),
                static::config()->get('data_source_classes')
            )
            ->setHasEmptyDefault(true)
            ->setDescription('The class of objects which will be used for variable substitution.')
        );
        return $fields;
    }

    public function getCMSCompositeValidator(): CompositeValidator
    {
        $validator = parent::getCMSCompositeValidator();
        $validator->addValidator(RequiredFields::create([
            'Name',
            'DataSourceClass',
        ]));
        return $validator;
    }

    public function getContent()
    {
        // This is a nasty way to get the styling in there but I just wanted my demo to be pretty so :p
        $content = $this->getField('Content');
        $style = '';
        if ($this->BGColor) {
            $style .= "body{background-color:$this->BGColor;}";
        }
        if ($this->FGColor) {
            $style .= "body{color:$this->FGColor;}a{color:$this->FGColor;}";
        }
        if ($style) {
            $content .= "<style>$style</style>";
        }
        return $content;
    }

    public function getPdfOptions(): array
    {
        $options = [];
        if ($this->ShowPageNumbers) {
            $options['footer-left'] = 'Page [page] of [topage]';
        }
        if ($this->ShowTitleInHeader) {
            $options['header-center'] = '[doctitle]';
        }
        return $options;
    }
}

<?php

namespace App\BeyondDocs\Pdf;

use InvalidArgumentException;
use SilverStripe\Assets\File;
use SilverStripe\Control\Controller;
use SilverStripe\Control\HTTPResponse;
use SilverStripe\Forms\FormAction;
use SilverStripe\ORM\CMSPreviewable;
use SilverStripe\ORM\DataObject;

class Client extends DataObject implements CMSPreviewable
{
    private static string $table_name = 'App_Pdf_Client';

    private static $db = [
        'Name' => 'Varchar(255)',
    ];

    private static $many_many = [
        // Obviously depending on your use case you could have
        // this relation go to some ConcretePdf class instead.
        'Pdfs' => File::class,
    ];

    private static array $summary_fields = [
        'Name',
        'Pdfs.Count' => 'Number of Pdfs'
    ];

    private static bool $show_unversioned_preview_link = true;

    public function getCMSFields()
    {
        // Adding a buttong like this is a bit of a hack - for now probably better to use something like https://github.com/lekoala/silverstripe-cms-actions
        $fields = parent::getCMSFields();
        $admin = PdfAdmin::singleton();
        $previewLink = Controller::join_links(
            $admin->Link(str_replace('\\', '-', $this->ClassName)),
            'cmsPreview',
            $this->ID
        );
        $previewBtn = FormAction::create('pdfs-btn', 'Show PDFs')
            ->addExtraClass('btn-info js-client-preview-pdfs')
            ->setAttribute('data-preview-url', $previewLink)
            ->setAttribute('data-text-alternate', 'Close PDFs')
            ->setAttribute('type', 'button')
            ->setUseButtonTag(true);
        $fields->add($previewBtn);
        return $fields;
    }

    /**
     * Send the PDF represented by $id in the Pdfs relation directly to the browser.
     * @throws InvalidArgumentException if $id doesn't refer to an item in the Pdfs relation, or it
     * doesn't have a file in the filesystem.
     */
    public function previewPDF(int $id): HTTPResponse
    {
        /** @var File $file */
        $file = $this->Pdfs()->find('ID', $id);
        if (!$file || !$file->exists()) {
            throw new InvalidArgumentException("PDF with ID $id does not exist for this client.");
        }
        /** @var HTTPResponse $response */
        $response = HTTPResponse::create($file->getString());
        $encodedFilename = rawurlencode($file->Filename);
        $disposition = "inline; filename=\"$file->Filename\"; filename*=UTF-8''$encodedFilename";
        $response->addHeader('Content-Disposition', $disposition);
        $response->addHeader('Content-Type', $file->getMimeType());
        return $response;
    }

    public function PreviewLink($action = null): ?string
    {
        // By default, don't preview anything at all. The preview panel will be used to display the PDFs
        // but the user will have to manually activate such a preview.
        return null;
    }

    public function CMSEditLink(): ?string
    {
        // This isn't strictly necessary in this specific example,
        // but it makes sense to implement a useful value since this method is mandatory.
        $admin = PdfTemplate::singleton();
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
        // The preview will only be used to show PDFs
        return 'application/pdf';
    }

}

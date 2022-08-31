<?php

namespace App\BeyondDocs\Pdf;

use SilverStripe\Control\Controller;

trait PdfPreviewable
{
    public function PreviewLink($action = null): ?string
    {
        if (!$this->isInDB()) {
            return null;
        }
        // The PdfAdmin will handle the cmsPreview path and output some previewable content for this object.
        $admin = PdfAdmin::singleton();
        return Controller::join_links(
            $admin->Link(str_replace('\\', '-', $this->ClassName)),
            'cmsPreview',
            $this->ID
        );
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
        return 'application/pdf';
    }
}

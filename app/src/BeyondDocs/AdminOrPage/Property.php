<?php

namespace App\BeyondDocs\AdminOrPage;

use SilverStripe\CMS\Controllers\CMSMain;
use SilverStripe\Control\Controller;
use SilverStripe\ORM\CMSPreviewable;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\Versioned\Versioned;

class Property extends DataObject implements CMSPreviewable
{
    private static string $table_name = 'App_AdminOrPage_Property';

    private static $db = [
        'Title' => 'Varchar(255)',
        'Address' => 'Varchar(255)',
        'TheOwner' => 'Varchar(255)',
    ];

    private static array $many_many = [
        'Pages' => PropertyPage::class,
    ];

    private static array $summary_fields = [
        'Title',
        'Address',
        'TheOwner',
    ];

    private static bool $show_stage_link = true;

    private static bool $show_live_link = true;

    private static array $extensions = [
        Versioned::class,
    ];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->removeByName('Pages');
        return $fields;
    }

    public function PreviewLink($action = null): ?string
    {
        // If the controller is CMSMain (aka pages section), preview in the context of the page being edited.
        if (($controller = Controller::curr()) instanceof CMSMain) {
            return $this->getPagePreviewLink($controller, $action);
        }
        // For literally any other context, only render the template for this object without any external context.
        return $this->getControllerPreviewLink();
    }

    /**
     * This is effectively the exact same implementation as is documented for modeladmin previews.
     */
    private function getControllerPreviewLink(): string
    {
        if (!$this->isInDB()) {
            return null;
        }
        return Controller::join_links(
            PropertyAdmin::singleton()->Link(str_replace('\\', '-', $this->ClassName)),
            'cmsPreview',
            $this->ID
        );
    }

    /**
     * This is effectively the exact same implementation as is documented for previewing objects on a page
     * EXCEPT we're getting the page from the controller instead of from the relation, since it could
     * theoretically be ANY page
     */
    private function getPagePreviewLink(CMSMain $controller, ?string $action): ?string
    {
        if ($page = $controller->currentPage()) {
            // Let the page know it's being previewed from a DataObject edit form (see BooksPage::MetaTags())
            $action = $action . '?DataObjectPreview=' . mt_rand();
            // Scroll the preview straight to where the object sits on the page.
            $link = $page->Link($action) . '#' . $this->getAnchor();
            return $link;
        }
        return null;
    }

    public function CMSEditLink(): ?string
    {
        // This isn't strictly necessary in this specific example,
        // but it makes sense to implement a useful value since this method is mandatory.
        // Note that I'm using the admin, not the page. The admin will always exist but this
        // property could exist without a corresponding page.
        $admin = PropertyAdmin::singleton();
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

    public function getAnchor(): string
    {
        return 'property-' . $this->getUniqueKey();
    }

    public function forTemplate(): DBHTMLText
    {
        // Uses the base Page template with a custom Layout template for this class
        return $this->renderWith(['type' => 'Includes', self::class]);
    }
}

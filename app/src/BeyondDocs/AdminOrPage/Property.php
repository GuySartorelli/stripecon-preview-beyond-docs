<?php

namespace App\BeyondDocs\AdminOrPage;

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

    private static array $has_one = [
        'Parent' => PropertyPage::class,
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
        $fields->removeByName('ParentID');
        return $fields;
    }

    public function PreviewLink($action = null): ?string
    {
        if (!$this->isInDB()) {
            return null;
        }
        if (($controller = Controller::curr()) instanceof PropertyAdmin) {
            return $this->getControllerPreviewLink($controller);
        }
        return $this->getPagePreviewLink($action);
    }

    private function getControllerPreviewLink(Controller $controller): string
    {
        return Controller::join_links(
            $controller->Link(str_replace('\\', '-', $this->ClassName)),
            'cmsPreview',
            $this->ID
        );
    }

    private function getPagePreviewLink(?string $action): ?string
    {
        // Let the page know it's being previewed from a DataObject edit form (see BooksPage::MetaTags())
        $action = $action . '?DataObjectPreview=' . mt_rand();
        // Scroll the preview straight to where the object sits on the page.
        if ($page = $this->Parent()) {
            $link = $page->Link($action) . '#' . $this->getAnchor();
            return $link;
        }
        return null;
    }

    public function CMSEditLink(): ?string
    {
        // This isn't strictly necessary in this specific example,
        // but it makes sense to implement a useful value since this method is mandatory.
        if (!$page = $this->Parent()) {
            return null;
        }
        return Controller::join_links(
            $page->CMSEditLink(),
            'field/Books/item',
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

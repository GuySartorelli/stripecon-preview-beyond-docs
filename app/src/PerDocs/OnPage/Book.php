<?php

namespace App\PerDocs\OnPage;

use SilverStripe\Control\Controller;
use SilverStripe\ORM\CMSPreviewable;
use SilverStripe\ORM\DataObject;
use SilverStripe\Versioned\Versioned;

class Book extends DataObject implements CMSPreviewable
{
    private static string $table_name = 'App_OnPage_Book';

    private static $db = [
        'Title' => 'Varchar(255)',
        'AuthorName' => 'Varchar(255)',
        'Genre' => 'Varchar(255)',
    ];

    private static array $has_one = [
        'Parent' => BooksPage::class,
    ];

    private static array $summary_fields = [
        'Title',
        'AuthorName',
        'Genre',
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
        return 'book-' . $this->getUniqueKey();
    }
}

<?php

namespace App\PerDocs\OnPage;

use Page;
use SilverStripe\Control\Controller;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RelationEditor;
use SilverStripe\View\Parsers\HTML4Value;

class BooksPage extends Page
{
    private static string $table_name = 'App_OnPage_BooksPage';

    private static array $has_many = [
        'Books' => Book::class,
    ];

    private static array $owns = [
        'Books',
    ];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->addFieldToTab('Root.Books', GridField::create(
            'Books',
            $this->fieldLabel('Books'),
            $this->Books(),
            GridFieldConfig_RelationEditor::create()
        ));
        return $fields;
    }

    public function MetaTags($includeTitle = true)
    {
        $tags = parent::MetaTags($includeTitle);
        if (!Controller::has_curr()) {
            return;
        }
        // If the 'DataObjectPreview' GET parameter is present, remove 'x-page-id' and 'x-cms-edit-link' meta tags.
        // This ensures that toggling between draft/published states doesn't revert the CMS to the page's edit form.
        $controller = Controller::curr();
        $request = $controller->getRequest();
        if ($request->getVar('DataObjectPreview') !== null) {
            $html = HTML4Value::create($tags);
            $xpath = "//meta[@name='x-page-id' or @name='x-cms-edit-link']";
            $removeTags = $html->query($xpath);
            $body = $html->getBody();
            foreach ($removeTags as $tag) {
                $body->removeChild($tag);
            }
            $tags = $html->getContent();
        }
        return $tags;
    }
}

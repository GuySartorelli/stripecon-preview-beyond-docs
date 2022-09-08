<?php

namespace App\PerDocs\ModelAsPage;

use Page;
use SilverStripe\CMS\Forms\SiteTreeURLSegmentField;
use SilverStripe\Control\ContentNegotiator;
use SilverStripe\Control\Controller;
use SilverStripe\Control\Director;
use SilverStripe\ORM\CMSPreviewable;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\Security\Permission;
use SilverStripe\Versioned\Versioned;
use SilverStripe\View\HTML;
use SilverStripe\View\Parsers\URLSegmentFilter;

class Region extends DataObject implements CMSPreviewable
{
    private static string $table_name = 'App_ModelAsPage_Region';

    private static array $db = [
        'Title' => 'Varchar(255)',
        'URLSegment' => 'Varchar(255)',
        'Content' => 'HTMLText',
    ];

    private static array $casting = [
        'MetaTags' => 'HTMLFragment',
    ];

    private static array $extensions = [
        Versioned::class,
    ];

    private static bool $show_stage_link = true;

    private static bool $show_live_link = true;

    private static array $summary_fields = [
        'Title',
        'URLSegment',
    ];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->replaceField(
            'URLSegment',
            SiteTreeURLSegmentField::create('URLSegment', $this->fieldLabel('URLSegment'))
            ->setURLPrefix('regions/')
            ->setURLSuffix('?stage=Stage')
        );
        return $fields;
    }

    /**
     * This is taken straight from SiteTree::onBeforeWrite() and trimmed down a bit.
     */
    public function onBeforeWrite(): void
    {
        if (!$this->URLSegment && $this->Title) {
            $this->URLSegment = $this->generateURLSegment($this->Title);
        } elseif ($this->isChanged('URLSegment', DataObject::CHANGE_VALUE)) {
            $this->URLSegment = $this->generateURLSegment($this->URLSegment);
        }
        parent::onBeforeWrite();
    }

    /**
     * This is taken straight from SiteTree::generateURLSegment() and trimmed down a bit.
     */
    private function generateURLSegment(string $title): string
    {
        $filter = URLSegmentFilter::create();
        $filteredTitle = $filter->filter($title);

        // Fallback to generic name if path is empty (no valid, convertable characters)
        if (!$filteredTitle || $filteredTitle == '-' || $filteredTitle == '-1') {
            $filteredTitle = "region-$this->ID";
        }

        return $filteredTitle;
    }

    /**
     * Again, taken from SiteTree and cut down to what we needed here
     */
    public function MetaTags(): string
    {
        $tagsArray = ['title' => [
                'tag' => 'title',
                'content' => $this->obj('Title')->forTemplate()
            ],
        ];

        $charset = ContentNegotiator::config()->uninherited('encoding');
        $tagsArray['contentType'] = [
            'attributes' => [
                'http-equiv' => 'Content-Type',
                'content' => 'text/html; charset=' . $charset,
            ],
        ];

        if (Permission::check('CMS_ACCESS_CMSMain') && $this->isInDB()) {
            $tagsArray['pageId'] = [
                'attributes' => [
                    'name' => 'x-page-id',
                    'content' => $this->ID,
                ],
            ];
            $tagsArray['cmsEditLink'] = [
                'attributes' => [
                    'name' => 'x-cms-edit-link',
                    'content' => $this->CMSEditLink(),
                ],
            ];
        }

        foreach ($tagsArray as $tagProps) {
            $tag = array_merge([
                'tag' => 'meta',
                'attributes' => [],
                'content' => null,
            ], $tagProps);
            $tags[] = HTML::createTag($tag['tag'], $tag['attributes'], $tag['content']);
        }

        return implode("\n", $tags);
    }

    public function AbsoluteLink(?string $action = null): ?string
    {
        if (!$link = $this->Link($action)) {
            return null;
        }
        return Director::absoluteURL($link);
    }

    public function Link(?string $action = null): ?string
    {
        if ($this->isInDB()) {
            return Controller::join_links('regions', $this->URLSegment, $action);
        }
        return null;
    }

    public function PreviewLink($action = null): ?string
    {
        return $this->Link();
    }

    public function CMSEditLink(): string
    {
        // This is actually used in this example, because we use the same MetaTags implementation as SiteTree
        $admin = RegionAdmin::singleton();
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
        // Uses the base Page template with a custom Layout template for this class
        return $this->renderWith([self::class, Page::class]);
    }
}

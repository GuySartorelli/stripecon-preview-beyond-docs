<?php

namespace App\PerDocs\ModelAsPage;

use SilverStripe\Control\Controller;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\ORM\FieldType\DBHTMLText;

class RegionController extends Controller
{
    private static $url_handlers = [
        '$Slug/$Action/$OtherID' => 'handleAction',
    ];

    public function index(HTTPRequest $request): DBHTMLText
    {
        if (($slug = $request->param('Slug')) && $region = Region::get()->find('URLSegment', $slug)) {
            return $region->forTemplate();
        }
        $this->httpError(404);
    }
}

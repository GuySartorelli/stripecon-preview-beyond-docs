<?php

namespace App\BeyondDocs\Pdf;

use SilverStripe\ORM\DataObject;

class Client extends DataObject
{
    private static string $table_name = 'App_Pdf_Client';

    private static $db = [
        'Name' => 'Varchar(255)',
    ];

    private static array $summary_fields = [
        'Name',
    ];
}

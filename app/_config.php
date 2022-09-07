<?php

use SilverStripe\Admin\CMSMenu;
use SilverStripe\Admin\SecurityAdmin;
use SilverStripe\AssetAdmin\Controller\AssetAdmin;
use SilverStripe\CampaignAdmin\CampaignAdmin;
use SilverStripe\Reports\ReportAdmin;
use SilverStripe\Security\PasswordValidator;
use SilverStripe\Security\Member;
use SilverStripe\SiteConfig\SiteConfigLeftAndMain;
use SilverStripe\VersionedAdmin\ArchiveAdmin;

// remove PasswordValidator for SilverStripe 5.0
$validator = PasswordValidator::create();
// Settings are registered via Injector configuration - see passwords.yml in framework
Member::set_password_validator($validator);

// Remove all the admin sections I don't care about
$removeAdmins = [
    ArchiveAdmin::class,
    AssetAdmin::class,
    CampaignAdmin::class,
    ReportAdmin::class,
    SecurityAdmin::class,
    SiteConfigLeftAndMain::class,
];
foreach ($removeAdmins as $class) {
    CMSMenu::remove_menu_class($class);
}

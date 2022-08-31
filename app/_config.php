<?php

use SilverStripe\Security\PasswordValidator;
use SilverStripe\Security\Member;

// remove PasswordValidator for SilverStripe 5.0
$validator = PasswordValidator::create();
// Settings are registered via Injector configuration - see passwords.yml in framework
Member::set_password_validator($validator);

// $c = '[image src="/assets/93ba4c36d0/success.png" id="1" width="128" height="128" class="leftAlone ss-htmleditorfield-file image"][';

// preg_match_all('/\[image\s+[^\]]*?id="(?<id>\d+)".*?\]/i', $c, $matches);
// echo '<pre>';
// preg_replace_callback('/\[image\s+[^\]]*?id="(?<id>\d+)".*?\]/i', function($matches) {
//     var_dump($matches);
// }, $c);
// echo '</pre>';

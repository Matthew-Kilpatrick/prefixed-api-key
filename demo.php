<?php
require 'vendor/autoload.php';
$pak = new \MatthewKilpatrick\PrefixedApiKey\PrefixedApiKey();
$components = $pak->generateApiKey('demo');

$token = $components->getToken();
$pak->checkApiKey($token, $components->getLongTokenHash());
<?php
header('Content-Type: text/txt; charset=utf8');

echo 'User-agent: Googlebot' . PHP_EOL;
echo 'Disallow: /nogooglebot/' . PHP_EOL . PHP_EOL;

echo 'User-agent: *' . PHP_EOL;
echo 'Allow: /' . PHP_EOL . PHP_EOL;

echo 'Sitemap: https://' . pathinfo($_SERVER['SERVER_NAME'], PATHINFO_BASENAME) . '/sitemap.xml';

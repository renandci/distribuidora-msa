<?php
header('Content-Type: text/html; charset=utf8');
$filter = filter_input(INPUT_GET, 'chave');
echo $filter;
<?php
require __DIR__ . '/common.php';

$auth0->logout('http://' . $_SERVER['HTTP_HOST']);

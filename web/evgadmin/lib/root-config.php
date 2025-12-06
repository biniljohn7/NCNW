<?php
$root = new stdClass();
$root->domain   = 'https://ncnw.haguetechnologies.io/';
$root->basedir  = dirname(dirname(__FILE__)) . '/';
$root->local    = false;
$root->appDomain = 'https://ncnw.haguetechnologies.io/';
$root->env = '/home/sivujkoxm7da/.env/ncnw';

if (ISLOCAL) {
    $host = $_SERVER['HTTP_HOST'];
    $root->domain   = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . $host . '/evergreenadmin/';
    $root->local = true;
    $root->appDomain = 'http://localhost:3000/evergreen/';
    $root->env = BASEDIR . '.env/';
}

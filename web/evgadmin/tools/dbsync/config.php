<?php
$config = new stdClass();
$config->local = new stdClass();
$config->live = new stdClass();

// local config
$config->local->url = 'http://localhost/evergreenadmin/evgadmin/tools/dbsync/';
$config->local->syncDir = dirname(__FILE__) . '/../../../QuerySync/';
$config->local->dbHost = 'localhost';
$config->local->dbUser = 'root';
$config->local->dbPass = '';
$config->local->dbName = 'evergreen';

// live config
include_once dirname(__FILE__) . '/../../lib/phpdotenv/vendor/autoload.php';
$envDir = '/home/sivujkoxm7da/.env/ncnw';
if (is_dir($envDir)) {
    $dotenv = Dotenv\Dotenv::createImmutable($envDir);
    $dotenv->load();
}

$config->live->url = 'https://ncnw.haguetechnologies.io/evgadmin/tools/dbsync/';
$config->live->syncDir = dirname(__FILE__) . '/query-sync-log/';
$config->live->dbHost = 'localhost';
$config->live->dbName = $_ENV['DBNAME'] ?? '';
$config->live->dbUser = $_ENV['DBUSER'] ?? '';
$config->live->dbPass = $_ENV['DBPASS'] ?? '';

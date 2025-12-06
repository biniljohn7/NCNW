<?php
$dbConf = new stdClass();
$dbConf->host = 'localhost';
$dbConf->db = 'evergreen';
$dbConf->user = 'root';
$dbConf->pass = '';

if (!ISLOCAL) {
    $dbConf->db = $_ENV['DBNAME'] ?? '';
    $dbConf->user = $_ENV['DBUSER'] ?? '';
    $dbConf->pass = $_ENV['DBPASS'] ?? '';
}

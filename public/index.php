<?php
date_default_timezone_set("Asia/Irkutsk");
error_reporting(E_ALL);
ini_set("display_errors", 1);

header("Content-type: text/html; charset=utf-8");

define("ROOT_DIR", realpath("../"));
$loader = require '../vendor/autoload.php';

function get_last_retrieve_url_contents_content_type () {
    return "Content-type: text/html; charset=windows-1251";
}

$client = new \Processor\Client();
$raw = $client->getSolutionsList();


$parser = new \Processor\Parser();
$solutionsLinks = $parser->parseSolutionsLinks($raw);

foreach($solutionsLinks as $linkId) {
    $solutionRaw = $client->getSolution($linkId);
    $info = $parser->parseSolution($solutionRaw);
    var_dump($info);
    break;
}

$view = new \Processor\View();

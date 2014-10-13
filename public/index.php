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

$type = isset($_GET["t"]) ? (integer) $_GET["t"] : 1;
$page = isset($_GET["p"]) ? (integer) $_GET["p"] : 1;

$raw = $client->getSolutionsList($type, $page);

$parser = new \Processor\Parser();
$solutionsLinks = $parser->parseSolutionsLinks($raw);


$solutions = [];
foreach($solutionsLinks as $linkId) {
    $solutionRaw = $client->getSolution($linkId);
    $info = $parser->parseSolution($solutionRaw);
    $solutions[] = $info;
}

$view = new \Processor\View();
$view->assign("solutions", $solutions);
$html = $view->render("list");
echo $html;




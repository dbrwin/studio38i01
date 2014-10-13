<?php
date_default_timezone_set("Asia/Irkutsk");
error_reporting(0);
ini_set("display_errors", 0);

header("Content-type: text/html; charset=utf-8");

include_once "../vendor/orbisnull/xhproflib/src/XHProfLib/XHProfLib.php";
\XHProfLib\XHProfiler::start('studio38i01');

define("ROOT_DIR", realpath("../"));
$loader = require '../vendor/autoload.php';

function get_last_retrieve_url_contents_content_type () {
    return "Content-type: text/html; charset=windows-1251";
}

$type = isset($_GET["t"]) ? (integer) $_GET["t"] : 1;
$page = isset($_GET["p"]) ? (integer) $_GET["p"] : 1;

try {
    $client = new \Processor\Client();
    $raw = $client->getSolutionsList($type, $page);
    $parser = new \Processor\Parser();
    $solutionsLinks = $parser->parseSolutionsLinks($raw);
    $maxPages = $parser->parseSolutionsListPagination($parser->prepareHtml($raw));
    $solutions = [];
    foreach ($solutionsLinks as $linkId) {
        $solutionRaw = $client->getSolution($linkId);
        $info = $parser->parseSolution($solutionRaw);
        $solutions[] = $info;
    }
    $view = new \Processor\View();
    $view->assign("solutions", $solutions);
    $view->assign("currentPage", $page);
    $view->assign("currentType", $type);
    $view->assign("maxPages", $maxPages);
    $html = $view->render("list");
    echo $html;
} catch (\Exception $e) {
    http_response_code(500);
    echo "<h1>Error</h1> \n";
}




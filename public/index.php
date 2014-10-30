<?php
date_default_timezone_set("Asia/Irkutsk");
error_reporting(0);
ini_set("display_errors", 0);

header("Content-type: text/html; charset=utf-8");

define("ROOT_DIR", realpath("../"));
$loader = require '../vendor/autoload.php';


$solution = isset($_GET["s"]) ? (integer) $_GET["s"] : null;

\DeltaDb\DbaStorage::setDefault(function () {
    $dbAdapter = new \DeltaDb\Adapter\MysqlPdoAdapter();
    $dbAdapter->connect('mysql:host=localhost;dbname=38studio', ["password" => "123"]);
    return $dbAdapter;
});

$storage = new \Processor\StorageMysql();
$view = new \Processor\View();
$view->setTemplateExtension("phtml");

if ($solution) {
    $solution = $storage->findOne(["linkid" => $solution]);
    $view->assign("solution", $solution);
    $html = $view->render("solution");
} else {
    $solutions = $storage->find();
    $view->assign("solutions", $solutions);
    $html = $view->render("solutions");
}

echo $html;




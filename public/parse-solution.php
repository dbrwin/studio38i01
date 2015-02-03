<?php
date_default_timezone_set("Asia/Irkutsk");
error_reporting(E_ALL);
ini_set("display_errors", 1);

header("Content-type: text/html; charset=utf-8");

define("ROOT_DIR", realpath("../"));
$loader = require '../vendor/autoload.php';

function get_last_retrieve_url_contents_content_type()
{
    return "Content-type: text/html; charset=windows-1251";
}

\DeltaDb\DbaStorage::setDefault(function () {
    $dbAdapter = new \DeltaDb\Adapter\MysqlPdoAdapter();
    $dbAdapter->connect('mysql:host=localhost;dbname=38studio', ["password" => "123"]);
    return $dbAdapter;
});

$types = Processor\Client::getSupportedTypes();
$logStorage = new \Processor\Log\LogStorageMysql();
$errorStorage = new \Processor\Log\ErrorStorageMysql();


$type = 1;

//work on one page
try {
    $client = new \Processor\Client();
    $parser = new \Processor\Parser();
    $parser->setHttpClient($client);

    //work on link on page
    $storage = new \Processor\StorageMysql();
    $linkId = 619114;
    $solution = $storage->findOne(["linkid" => (integer)$linkId]);
    if (!empty($solution)) {
        $solutionId = $solution->getId();
    }
    $solutionRaw = $client->getSolution($linkId);
    if (!$solutionRaw) {
        throw new Exception("Error in get solution #{$linkId}");
    }
    $solution = $parser->parseSolution($solutionRaw);
    $solution->setLinkid($linkId);
    $solution->setGroup($type);
    $solution->setRaw($solutionRaw);
    if ($solutionId) {
        $solution->setId($solutionId);
    }

    $reviewRaw = $client->getResponse($linkId);
    if ($reviewRaw) {
        $review = $parser->parseReview($reviewRaw, $linkId);
        if ($review) {
            $solution->setReview($review);
        }
    }

} catch (\Exception $e) {
    $errorParams = [
        "type" => $type,
    ];
    if (isset($page)) {
        $errorParams["page"] = $page;
    }
    if (isset($linkId)) {
        $errorParams["linkId"] = $linkId;
    }

    $log = new \Monolog\Logger("parser");
    $log->pushHandler(new \Monolog\Handler\StreamHandler(ROOT_DIR . '/data/log/parser.log', \Monolog\Logger::DEBUG));
    $message = "error #{$e->getCode()} in parsing [" . var_export($errorParams, true) . "] :: {$e->getMessage()} [{$e->getFile()}:{$e->getLine()}";
    $log->error($message);
    /** @var \Processor\Log\ErrorEntity $error */
}


var_dump($solution);


echo "Done...";



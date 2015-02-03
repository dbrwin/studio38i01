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


foreach ($types as $type) {
    do {
//work on one page
        try {
            $logEntitys = $logStorage->find(["endTime" => null, "type" => $type], null, 1, null, ["startTime", "DESC"]);
            if (!empty($logEntitys)) {
                $logEntity = reset($logEntitys);
                $page = $logEntity->getPage() + 1;
            } else {
                $page = 1;
                $logEntity = $logStorage->create();
                $logEntity->setType($type);
            }
            echo "work on type: {$type} page: {$page} \n";

            $client = new \Processor\Client();
            $raw = $client->getSolutionsList($type, $page);
            $parser = new \Processor\Parser();
            $parser->setHttpClient($client);
            $solutionsLinks = $parser->parseSolutionsLinks($raw);
            $maxPages = $parser->parseSolutionsListPagination($parser->prepareHtml($raw));
            $nextPage = ($page + 1) <= $maxPages ? ($page + 1) : null;

            //work on link on page
            $storage = new \Processor\StorageMysql();
            foreach ($solutionsLinks as $num => $linkId) {
                $solution = $storage->findOne(["linkid" => (integer)$linkId]);
                if ($solution) {
                    continue;
                }
                $solutionRaw = $client->getSolution($linkId);
                if (!$solutionRaw) {
                    throw new Exception("Error in get solution #{$linkId}");
                }
                $solution = $parser->parseSolution($solutionRaw);
                $solution->setLinkid($linkId);
                $solution->setGroup($type);
                $solution->setRaw($solutionRaw);

                $reviewRaw = $client->getResponse($linkId);
                if ($reviewRaw) {
                    $review = $parser->parseReview($reviewRaw, $linkId);
                    if ($review) {
                        $solution->setReview($review);
                    }
                }
                $saveResult = $storage->save($solution);
                if (!$saveResult) {
                    throw new \Exception("Not saved #{$linkId} g.{$type}");
                }
            }
            //page done
            $logEntity->setPage($page);
            if (!$nextPage) {
                $logEntity->setEndTime(new DateTime());
            }
            $logStorage->save($logEntity);
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
            echo $message;
            $log->error($message);
            /** @var \Processor\Log\ErrorEntity $error */
            $error = $errorStorage->create();
            $error->loadFromException($e);
            $error->setParams($errorParams);
            $errorStorage->save($error);
        }
//end work in one page
    } while ($nextPage);
}


echo "Done...";



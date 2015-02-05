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

$scriptStartTime = new DateTime();

$errorsCount = 0;

foreach ($types as $type) {
    echo "work on type: {$type}\n";
    do {
//work on one page
        try {
            $logEntitys = $logStorage->find(["endTime" => null, "type" => $type], null, 1, null, ["startTime", "DESC"]);
            if (!empty($logEntitys)) {
                /** @var \Processor\Log\LogEntity $logEntity */
                $logEntity = reset($logEntitys);
                $page = $logEntity->getPage() + 1;
                $startTime = $logEntity->getStartTime();
                echo "  continue session " . $startTime->format("d.m.Y h:i:s") .  PHP_EOL;
            } else {
                $page = 1;
                $logEntity = $logStorage->create();
                $logEntity->setType($type);
                $startTime = $logEntity->getStartTime();
                echo "  start new session #" . $startTime->format("d.m.Y h:i:s") . PHP_EOL;
            }
            echo "    work on  page: {$page} \n";

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
                echo "      solution #{$linkId} => ";
                $solution = $storage->findOne(["linkid" => (integer)$linkId]);
                if ($solution) {
                    echo "skip (already have) \n";
                    continue;
                }
                echo "get... ";
                $solutionRaw = $client->getSolution($linkId);
                if (!$solutionRaw) {
                    throw new Exception("Error in get solution #{$linkId}");
                }
                echo "parse... ";
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
                echo "save... ";
                $saveResult = $storage->save($solution);
                if (!$saveResult) {
                    throw new \Exception("Not saved #{$linkId} g.{$type}");
                }
                echo " done \n";
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
            echo "\n" . $message . "\n";
            $log->error($message);
            /** @var \Processor\Log\ErrorEntity $error */
            $error = $errorStorage->create();
            $error->loadFromException($e);
            $error->setParams($errorParams);
            $errorStorage->save($error);
            $errorsCount++;
        }
//end work in one page
        $diff = $startTime->diff(new DateTime())->format("%d days, %h hours, %i minuts, %s seconds");
        echo "    time {$diff} \n";
    } while ($nextPage);
}

echo "Done All... \n";

if ($errorsCount) {
    echo "Errors: {$errorsCount} \n";
}

$diff = $scriptStartTime->diff(new DateTime())->format("%d days, %h hours, %i minuts, %s seconds");
echo "time to work {$diff} \n";


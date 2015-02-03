<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace Processor\Log;


use DeltaDb\Repository;

class LogStorageMysql extends Repository
{
    protected $metaInfo = [
        "log_load" => [
            "class"  => "\\Processor\\Log\\LogEntity",
            "id"     => "id",
            "fields" => [
                "id",
                "type",
                "startTime",
                "endTime",
                "page",
            ]
        ],
    ];

}
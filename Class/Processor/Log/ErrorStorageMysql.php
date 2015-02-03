<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace Processor\Log;


use DeltaDb\Repository;
use DeltaDb\EntityInterface;

class ErrorStorageMysql extends Repository
{
    protected $metaInfo = [
        "log_error" => [
            "class"  => "\\Processor\\Log\\ErrorEntity",
            "id"     => "id",
            "fields" => [
                "id",
                "time",
                "exceptionClass",
                "code",
                "message",
                "file",
                "line",
                "params",
                "backtrace"
            ]
        ],
    ];

    public function reserve(EntityInterface $entity)
    {
        $data = parent::reserve($entity);
        $data = $this->serializeFields($data, ["params", "backtrace"]);
        return $data;
    }

    public function load(EntityInterface $entity, array $data)
    {
        $data = $this->unserializeFields($data, ["params", "backtrace"]);
        parent::load($entity, $data);
    }

}
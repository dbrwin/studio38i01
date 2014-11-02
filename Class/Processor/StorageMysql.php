<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace Processor;


use DeltaDb\EntityInterface;
use DeltaDb\Repository;

class StorageMysql extends Repository
{
    protected $metaInfo = [
        "solutions" => [
            "class"  => "\\Processor\\Solution",
            "id"     => "id",
            "fields" => [
                "id",
                "linkid",
                "group",
                "organization",
                "city",
                "industry",
                "type",
                "functions",
                "arms",
                "date",
                "text",
                "reviewtext",
                "revdocs",
                "raw"
            ]
        ],
    ];

    public function reserve(EntityInterface $entity)
    {
        $data = parent::reserve($entity);
        if (isset($data["fields"]["revdocs"]) && is_array($data["fields"]["revdocs"])) {
            $data["fields"]["revdocs"] = serialize($data["fields"]["revdocs"]);
        }
        return $data;
    }

    public function load(EntityInterface $entity, array $data)
    {
        if (isset($data["revdocs"]) && is_string($data["revdocs"])) {
            $revdocs = unserialize($data["revdocs"]);
            $data["revdocs"] = $revdocs;
        }
        parent::load($entity, $data);
    }


} 
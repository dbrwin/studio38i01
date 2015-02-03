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
                "title",
                "group",
                "ptype",
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
        $data = $this->serializeFields($data, ["revdocs", "functions"]);
        return $data;
    }

    public function load(EntityInterface $entity, array $data)
    {
        $data = $this->unserializeFields($data, ["revdocs", "functions"]);
        parent::load($entity, $data);
    }

} 
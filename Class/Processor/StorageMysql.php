<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace Processor;


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
                "reviews",
                "revdocs",
                "raw"
            ]
        ],
    ];

} 
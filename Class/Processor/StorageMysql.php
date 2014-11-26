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

    public function fieldArraySave($data, $fieldName)
    {
        if (isset($data["fields"][$fieldName]) && is_array($data["fields"][$fieldName])) {
            $data["fields"][$fieldName] = serialize($data["fields"][$fieldName]);
        }
        return $data;
    }

    public function fieldArrayLoad($data, $fieldName)
    {
        if (isset($data[$fieldName])) {
            $value = @unserialize($data[$fieldName]);
            $data[$fieldName] = $value ?: [];
        }
        return $data;
    }

    public function reserve(EntityInterface $entity)
    {
        $data = parent::reserve($entity);
        $data = $this->fieldArraySave($data, "revdocs");
        $data = $this->fieldArraySave($data, "functions");
        return $data;
    }

    public function load(EntityInterface $entity, array $data)
    {
        $data = $this->fieldArrayLoad($data, "revdocs");
        $data = $this->fieldArrayLoad($data, "functions");
        parent::load($entity, $data);
    }


} 
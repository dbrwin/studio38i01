<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace Processor;


class View extends \dTpl\View
{
    public function getConfig()
    {
        return ["templateDirs" => ["templates"]];
    }


} 
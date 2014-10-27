<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace Processor;


class StoragePhp
{
    protected $path;

    function __construct($path = null)
    {
        if ($path) {
            $this->setPath($path);
        }
    }


    /**
     * @return mixed
     */
    public function getPath()
    {
        if (!$this->path)  {
            $this->path = ROOT_DIR . "/data/solutions.pdb";
        }
        return $this->path;
    }

    /**
     * @param mixed $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }


    public function save($data)
    {
        $data = serialize($data);
        $path = $this->getPath();
        file_put_contents($path, $data);
    }

    public function load()
    {
        $path = $this->getPath();
        $data = file_get_contents($path);
        $data = unserialize($data);
        return $data;
    }


} 
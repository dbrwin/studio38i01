<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace Controller;


use DeltaCore\AbstractController;
use Processor\StorageMysql;

class IndexController extends AbstractController
{
    /**
     * @return StorageMysql
     */
    public function getStorage()
    {
        return $this->getApplication()["Storage"];
    }

    public function listAction()
    {
        $storage = $this->getStorage();
        $solutions = $storage->find();
        $this->getView()->assign("items", $solutions);
    }

    public function viewAction()
    {
        $id = $this->getRequest()->getUriPartByNum(1);
        $id = substr($id, 2);
        $id = (integer) $id;
        $solution = $this->getStorage()->findById($id);
        $this->getView()->assign("item", $solution);
    }



} 
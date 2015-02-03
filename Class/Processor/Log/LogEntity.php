<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace Processor\Log;

use DeltaCore\Prototype\AbstractEntity;
use DeltaDb\EntityInterface;

class LogEntity extends AbstractEntity implements EntityInterface
{
    protected $type;
    /** @var  \DateTime */
    protected $startTime;
    /** @var  \DateTime */
    protected $endTime;
    protected $page;

    function __construct()
    {
        $this->setStartTime(new \DateTime());
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return \DateTime
     */
    public function getStartTime()
    {
        if ($this->startTime && !$this->startTime instanceof \DateTime) {
            $this->startTime = new \DateTime($this->startTime);
        }
        return $this->startTime;
    }

    /**
     * @param \DateTime $startTime
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;
    }

    /**
     * @return \DateTime
     */
    public function getEndTime()
    {
        if ($this->endTime && !$this->endTime instanceof \DateTime) {
            $this->endTime = new \DateTime($this->endTime);
        }
        return $this->endTime;
    }

    /**
     * @param \DateTime $endTime
     */
    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;
    }

    /**
     * @return mixed
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @param mixed $page
     */
    public function setPage($page)
    {
        $this->page = $page;
    }

}
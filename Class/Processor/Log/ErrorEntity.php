<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace Processor\Log;


use DeltaCore\Prototype\AbstractEntity;
use DeltaDb\EntityInterface;

class ErrorEntity extends AbstractEntity implements EntityInterface
{
    protected $time;
    protected $exceptionClass;
    protected $code;
    protected $message;
    protected $file;
    protected $line;
    protected $params;
    protected $backtrace;

    function __construct()
    {
        $this->setTime(new \DateTime());
    }

    /**
     * @return mixed
     */
    public function getTime()
    {
        if ($this->time && !$this->time instanceof \DateTime) {
            $this->time = new \DateTime($this->time);
        }
        return $this->time;
    }

    /**
     * @param mixed $time
     */
    public function setTime($time)
    {
        $this->time = $time;
    }

    /**
     * @return mixed
     */
    public function getExceptionClass()
    {
        return $this->exceptionClass;
    }

    /**
     * @param mixed $exceptionClass
     */
    public function setExceptionClass($exceptionClass)
    {
        $this->exceptionClass = $exceptionClass;
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param mixed $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param mixed $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return mixed
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param mixed $file
     */
    public function setFile($file)
    {
        $this->file = $file;
    }

    /**
     * @return mixed
     */
    public function getLine()
    {
        return $this->line;
    }

    /**
     * @param mixed $line
     */
    public function setLine($line)
    {
        $this->line = $line;
    }

    /**
     * @return mixed
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param mixed $params
     */
    public function setParams(array $params)
    {
        $this->params = $params;
    }

    /**
     * @return mixed
     */
    public function getBacktrace()
    {
        return $this->backtrace;
    }

    /**
     * @param mixed $backtrace
     */
    public function setBacktrace(array $backtrace)
    {
        $this->backtrace = $backtrace;
    }

    public function loadFromException(\Exception $e)
    {
        $this->setExceptionClass(get_class($e));
        $this->setCode($e->getCode());
        $this->setMessage($e->getMessage());
        $this->setFile($e->getFile());
        $this->setLine($e->getLine());
        $this->setBacktrace(debug_backtrace());
    }

}
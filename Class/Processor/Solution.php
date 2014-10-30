<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace Processor;


use DeltaCore\Prototype\AbstractEntity;
use DeltaDb\EntityInterface;

class Solution extends AbstractEntity implements EntityInterface
{
    protected $linkid;
    protected $group;
    protected $organization;
    protected $city;
    protected $industry;
    protected $type;
    protected $functions;
    protected $arms;
    protected $date;
    protected $text;
    protected $reviewtext;
    protected $revdocs;
    protected $raw;

    /**
     * @return mixed
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @param mixed $group
     */
    public function setGroup($group)
    {
        $this->group = $group;
    }

    /**
     * @return mixed
     */
    public function getLinkid()
    {
        return $this->linkid;
    }

    /**
     * @param mixed $linkid
     */
    public function setLinkid($linkid)
    {
        $this->linkid = $linkid;
    }

    /**
     * @return mixed
     */
    public function getArms()
    {
        return $this->arms;
    }

    /**
     * @param mixed $armCount
     */
    public function setArms($armCount)
    {
        $this->arms = $armCount;
    }

    /**
     * @return mixed
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param mixed $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @return mixed
     */
    public function getFunctions()
    {
        return $this->functions;
    }

    /**
     * @param mixed $functions
     */
    public function setFunctions($functions)
    {
        $this->functions = $functions;
    }

    /**
     * @return mixed
     */
    public function getIndustry()
    {
        return $this->industry;
    }

    /**
     * @param mixed $industry
     */
    public function setIndustry($industry)
    {
        $this->industry = $industry;
    }

    /**
     * @return mixed
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * @param mixed $organization
     */
    public function setOrganization($organization)
    {
        $this->organization = $organization;
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
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param mixed $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * @return mixed
     */
    public function getRevdocs()
    {
        return $this->revdocs;
    }

    /**
     * @param mixed $revdocs
     */
    public function setRevdocs($revdocs)
    {
        $this->revdocs = $revdocs;
    }

    /**
     * @return mixed
     */
    public function getReviewtext()
    {
        return $this->reviewtext;
    }

    /**
     * @param mixed $reviews
     */
    public function setReviewText($reviews)
    {
        $this->reviewtext = $reviews;
    }

    /**
     * @return mixed
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param mixed $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * @return mixed
     */
    public function getRaw()
    {
        return $this->raw;
    }

    /**
     * @param mixed $raw
     */
    public function setRaw($raw)
    {
        $this->raw = $raw;
    }

    public function setReview(Review $review)
    {
        $this->setReviewtext($review->getText());
        $this->setRevdocs($review->getFiles());
    }

}
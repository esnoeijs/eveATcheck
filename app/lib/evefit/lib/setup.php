<?php
/**
 * Created by PhpStorm.
 * User: erik
 * Date: 3/22/14
 * Time: 1:08 PM
 */

namespace eveATcheck\lib\evefit\lib;


class setup
{
    protected $new;

    protected $id;
    protected $name;
    protected $desc;

    protected $publishDate;
    protected $updateDate;
    protected $ownerId;
    protected $ownerName;
    protected $shipSort;

    /**
     * @return \DateTime
     */
    public function getUpdateDate()
    {
        return $this->updateDate;
    }

    /**
     * @return mixed
     */
    public function getOwnerId()
    {
        return $this->ownerId;
    }

    /**
     * @return mixed
     */
    public function getOwnerName()
    {
        return $this->ownerName;
    }

    /**
     * @return \DateTime
     */
    public function getPublishDate()
    {
        return $this->publishDate;
    }

    protected $points = array();


    protected $deletedFits = array();

    protected $needsSave = false;


    protected $warnings = array();


    /**
     * @var fit[]
     */
    protected $fits = array();

    public function __construct($id, $name, $desc, $ownerId, $ownerName, $publishDate=null, $updateDate=null)
    {
        $this->ownerId   = $ownerId;
        $this->ownerName = $ownerName;
        $this->name = $name;
        $this->desc = $desc;

        if (!is_numeric($id))
        {
            $this->id  = str_replace(' ','_', uniqid($name));
            $this->new = true;
            $this->publishDate = new \DateTime('now');
            $this->updateDate = new \DateTime('now');
        }else{
            $this->id  = $id;
            $this->new = false;
            $this->publishDate = new \DateTime($publishDate);
            $this->updateDate = new \DateTime($updateDate);
        }
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getDesc()
    {
        return $this->desc;
    }

    public function isNew()
    {
        return $this->new;
    }

    /**
     * @param boolean $needsSave
     */
    public function setNeedsSave($needsSave)
    {
        $this->needsSave = $needsSave;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @param mixed $desc
     */
    public function setDesc($desc)
    {
        $this->desc = $desc;
    }

    /**
     * @return boolean
     */
    public function getNeedsSave()
    {
        return $this->needsSave;
    }

    public function getFit($fitId)
    {
        foreach ($this->fits as $fit)
        {
            if ($fit->getId() == $fitId)
                return $fit;
        }
        return false;
    }

    public function getFits()
    {
        return $this->fits;
    }

    public function setFits($fits)
    {
        $this->fits = $fits;
        $this->sortFits();
    }

    public function deleteFit($fitId)
    {
        foreach ($this->fits as $key => $fit)
        {
            if ($fit->getId() == $fitId)
            {
                unset($this->fits[$key]);
                $this->deletedFits[] = $fitId;
                return true;
            }
        }
        return false;
    }

    public function addFit($fit)
    {
        $this->fits[] = $fit;
        $this->sortFits();
    }

    public function replaceFit($fitId, $newFit)
    {
        foreach ($this->fits as $key => $fit)
        {
            print $fit->getId()." - " . $fitId."<br/>";
            if ($fit->getId() == $fitId)
            {
                $this->fits[$key] = $newFit;
                return true;
            }
        }
        return false;
    }

    public function setPoints($points)
    {
        $this->points = $points;
    }

    public function getPoints()
    {
        return $this->points;
    }

    public function getPilots()
    {
        $pilots = 0;
        foreach ($this->fits as $fit)
            $pilots += $fit->getQuantity();

        return $pilots;
    }

    public function getWarnings()
    {
        return $this->warnings;
    }

    public function hasWarning()
    {
        return (count($this->warnings)>0);
    }

    public function setWarning($tournament, $warning)
    {
        $this->warnings[] = array('tournament' => $tournament, 'text' => $warning);
    }

    /**
     * @return array of int
     */
    public function getDeletedFits()
    {
        return $this->deletedFits;
    }

    /**
     * Sorts the fits alphabetically
     */
    private function sortFits()
    {
        switch ($this->shipSort) {
            case 'points':
                usort($this->fits, function(fit $fitA, fit $fitB) {
                    if ($fitA->getPoints() == $fitB->getPoints()) {
                        return 0;
                    }
                    return ($fitA->getPoints() > $fitB->getPoints()) ? -1 : 1;
                });
                break;
            case 'alpha':
            default:
                usort($this->fits, function(fit $fitA, fit $fitB) {
                    if ($fitA->getType() == $fitB->getType()) {
                        return 0;
                    }
                    return ($fitA->getType() < $fitB->getType()) ? -1 : 1;
                });
                break;
        }
    }

    public function setSort($sortOrder)
    {
        if (!in_array($sortOrder, ['points','alpha'])) return;
        $this->shipSort = $sortOrder;

        $this->sortFits();
    }
} 
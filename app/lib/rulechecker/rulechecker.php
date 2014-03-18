<?php
/**
 * Created by PhpStorm.
 * User: erik
 * Date: 3/13/14
 * Time: 3:56 AM
 */

namespace eveATcheck\lib\rulechecker;


use eveATcheck\lib\database\database;
use eveATcheck\lib\evefit\evefit;
use eveATcheck\lib\evefit\lib\fit;
use eveATcheck\lib\rulechecker\lib\tournament;

/**
 * Class rulechecker
 *
 * Creates tournament objects that fits and setups can be checked against for their rules.
 *
 * @package eveATcheck\lib\rulechecker
 */
class rulechecker
{
    private $db;
    private $files;

    /**
     * @var tournament[]
     */
    private $tournaments;

    /**
     * return instance of rulechecker
     *
     * @param database $db
     */
    public function __construct($db)
    {
        $this->db = $db;
        $this->tournaments = $this->getTournaments();
    }

    /**
     * Returns array of tournament objects from found XML files.
     *
     * @todo add XML error checking.
     * @return array
     */
    protected function getTournaments()
    {
        $tournaments = array();
        $dir = new \DirectoryIterator('../rules/');
        foreach ($dir as $fileInfo) {
            if($fileInfo->isDot()) continue;
            if (strstr($fileInfo->getFilename(), '.xml'))
            {
                $tournaments[] = new tournament(simplexml_load_file($dir->getPath() . DIRECTORY_SEPARATOR .  $fileInfo->getFilename()));
            }
        }
        return $tournaments;
    }

    public function checkFit(fit $fit)
    {
        foreach ($this->tournaments as $tournament)
        {
            $tournament->checkFit($fit);
        }

        return $fit;
    }

    public function checkSetup(evesetup $setup)
    {
        // check setup against the rules
    }




} 
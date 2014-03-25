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
use eveATcheck\lib\evefit\lib\setup;
use eveATcheck\lib\evemodel\evemodel;
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
    protected $model;
    protected $files;

    /**
     * @var tournament[]
     */
    protected $tournaments;

    /**
     * return instance of rulechecker
     *
     * @param database $db
     */
    public function __construct(evemodel $model)
    {
        $this->model = $model;
        $this->tournaments = $this->loadTournaments();
    }

    /**
     * Returns array of tournament objects from found XML files.
     *
     * @todo add XML error checking.
     * @return array
     */
    protected function loadTournaments()
    {
        $tournaments = array();
        $dir = new \DirectoryIterator('../rules/');
        foreach ($dir as $fileInfo) {
            if($fileInfo->isDot()) continue;
            if (strstr($fileInfo->getFilename(), '.xml'))
            {
                $tournaments[] = new tournament(simplexml_load_file($dir->getPath() . DIRECTORY_SEPARATOR .  $fileInfo->getFilename()), $this->model);
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

    public function checkSetup(setup $setup)
    {

        foreach ($this->tournaments as $tournament)
        {
            $setup = $tournament->checkSetup($setup);
        }

        return $setup;
        // check setup against the rules
    }
}
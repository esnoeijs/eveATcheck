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
     * @var tournament
     */
    protected $tournament;

    /**
     * return instance of rulechecker
     *
     * @param database $db
     */
    public function __construct(evemodel $model, $tournamentFile)
    {
        $this->model = $model;
        $this->tournament = $this->loadTournament($tournamentFile);
    }

    /**
     * Returns tournament objects from found XML file.
     *
     * @todo add XML error checking.
     * @return tournament
     */
    protected function loadTournament($tournamentFile)
    {
        if (!is_file($tournamentFile)) throw new \Exception('Could not find tournament rules for: '.$tournamentFile);

        return new tournament(simplexml_load_file($tournamentFile), $this->model);
    }

    public function checkFit(fit $fit)
    {
        $this->tournament->checkFit($fit);

        return $fit;
    }

    public function checkSetup(setup $setup)
    {
        $this->tournament->checkSetup($setup);

        return $setup;
    }

    public function getTournament()
    {
        return $this->tournament;
    }
}
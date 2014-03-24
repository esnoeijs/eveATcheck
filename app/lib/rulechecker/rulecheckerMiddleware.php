<?php
/**
 * Created by PhpStorm.
 * User: erik
 * Date: 3/24/14
 * Time: 3:43 PM
 */

namespace eveATcheck\lib\rulechecker;


use Slim\Middleware;


/**
 * Middleware that will always run and check fits and setups agains the rules
 *
 * Class rulecheckerMiddleware
 * @package eveATcheck\lib\rulechecker
 */
class rulecheckerMiddleware extends Middleware
{
    public function call()
    {
        $setups      = $this->app->evefit->getSetups();
        $ruleChecker = $this->app->rulechecker;

        foreach ($setups as &$setup)
        {
            $setup = $ruleChecker->checkSetup($setup);
        }
        // @todo I don't like this.
        $this->app->evefit->setSetups($setups);

        $this->next->call();
    }



}
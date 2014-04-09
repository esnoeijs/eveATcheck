<?php
/**
 * Created by PhpStorm.
 * User: erik
 * Date: 3/22/14
 * Time: 2:00 PM
 */

namespace eveATcheck\controller;


use eveATcheck\lib\evefit\evefit;
use Slim\Slim;

class setup
{
    /**
     * Returns the form HTML to create a new setup.
     * @param Slim $app
     */
    public function action_addDialog(Slim $app)
    {
        if (!$app->user->isLoggedin()) return false;

        $app->render('setup/addDialog.twig');
    }


    public function action_editDialog(Slim $app, $setupId)
    {
        if (!$app->user->isLoggedin()) return false;

        $setup = $app->evefit->getSetup($setupId);

        $app->render('setup/editDialog.twig', array('setup' => $setup));
    }

    public function action_update(Slim $app, $setupId)
    {
        if (!$app->user->isLoggedin()) return false;

        /** @var \eveATcheck\lib\evefit\lib\setup $setup */
        $setup = $app->evefit->getSetup($setupId);
        $setup->setName($app->request()->post('name'));
        $setup->setDesc($app->request()->post('description'));
        $setup->setNeedsSave(true);

        $app->evefit->save();
    }

    /**
     * Returns the form HTML to create a new setup.
     * @param Slim $app
     */
    public function action_quickAddDialog(Slim $app)
    {
        if (!$app->user->isLoggedin()) return false;

        $tour   = $app->rulechecker->getTournament();


        $app->render('setup/quickAddDialog.twig', array('tournament' => $tour));
    }


    public function action_shipAutocomplete(Slim $app)
    {
        $maxRows   = $app->request()->get('maxrows');
        $nameStart = $app->request()->get('name_startsWith');


        $points = $app->rulechecker->getTournament()->getPointCategories();

        $result = array();
        foreach ($points as $categories)
        {
            foreach ($categories['ships'] as $ship)
            {
                if (stripos($ship, $nameStart)===0)
                {
                    $result[] = array(
                        'name' => $ship,
                        'category' => $categories['name'],
                        'points' => $categories['points']
                    );
                }
            }
        }

        print json_encode($result);
    }

    /**
     * Creates a new empty setup with the given name and description and
     * adds it to the user session
     *
     * @param Slim $app
     */
    public function action_add(Slim $app)
    {
        if (!$app->user->isLoggedin()) return false;

        $name = $app->request()->post('name');
        $desc = $app->request()->post('description');

        $setup = new \eveATcheck\lib\evefit\lib\setup(null, $name, $desc, $app->user->getId());
        $app->evefit->addSetup($setup, false);

        // If this is a quickAdd submission gather the submitted ships and add them to the setup.
        if ($app->request()->post('quickAdd'))
        {
            foreach ($app->request()->post() as $name => $value)
            {
                if (preg_match('/ship_([0-9]+$)/', $name, $match))
                {
                    $idx = $match[1];

                    $shipName = $app->request()->post('ship_'.$idx);
                    $shipQty  = $app->request()->post('ship_'.$idx.'_qty');

                    $app->evefit->addFit("[{$shipName}, {$setup->getName()}_{$shipName}]", "auto-generated", $shipQty, $setup->getId(), false);
                }
            }
        }


        $app->evefit->save();

        return;
    }

    /**
     * Deletes a setup from the user session.
     *
     * @param Slim $app
     * @param String $setupId
     */
    public function action_delete(Slim $app, $setupId)
    {
        if (!$app->user->isLoggedin()) return false;

        $app->evefit->deleteSetup($setupId);
    }

}
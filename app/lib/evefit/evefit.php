<?php
/**
 * Created by PhpStorm.
 * User: erik
 * Date: 3/11/14
 * Time: 4:34 PM
 */

namespace eveATcheck\lib\evefit;


use eveATcheck\lib\evefit\lib\fit;

class evefit
{
    protected $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Parses EFT format text to extract eve fits
     *
     * Example EFT fit:
     * [Nemesis, hank's Nemesis]
     * Ballistic Control System I
     * Micro Auxiliary Power Core I
     *
     * Experimental 1MN Afterburner I
     * J5 Prototype Warp Disruptor I
     * Medium Azeotropic Ward Salubrity I
     * Small Electrochemical Capacitor Booster I,Cap Booster 150
     *
     * Covert Ops Cloaking Device II
     * Prototype 'Arbalest' Torpedo Launcher,Inferno Torpedo
     * Prototype 'Arbalest' Torpedo Launcher,Inferno Torpedo
     * Prototype 'Arbalest' Torpedo Launcher,Inferno Torpedo
     *
     * Small Ancillary Current Router I
     * Small Ancillary Current Router I
     *
     *
     * @todo rewrite fit parsing to use the DB to find out which slot an item belongs too and not try to guess it
     * @param $fit
     */
    public function parseEFT($fitEFT)
    {
        $fitEFT = new \ArrayIterator(explode(PHP_EOL, $fitEFT));

        while ($fitEFT->valid())
        {
            $fits    = array();
            $fitline = $fitEFT->current();

            // Look for EFT format header
            preg_match('/^\[([a-zA-Z ]+), (.*)]/', $fitline, $matches);
            $fitEFT->next();

            if (count($matches)!==0)
            {
                $fit = new fit($this->db, $matches[1], $matches[2]);

                // EFT will have listed modules separated by a double linebreak to separate between slot types
                foreach (array(fit::LOWSLOT, fit::MIDSLOT, fit::HIGHSLOT, fit::RIGSLOT, fit::SUBSYSTEM) as $slotType)
                {
                    while ($fitEFT->valid())
                    {
                        $fitline = $fitEFT->current();

                        // Module Name, Charge type
                        preg_match('/([^,]*)(,(.*))?/', $fitline, $matches);

                        // Empty line signifies end of block of modules for this slot type.
                        if (trim($matches[0])=="")
                        {
                            $fitEFT->next();
                            break;
                        }
                        else
                        {
                            $charge = null;
                            $module = $matches[1];
                            if (isset($matches[3]))
                            {
                                $charge = $matches[3];
                            }

                            $fit->addModule($module, $charge, $slotType);
                        }

                        $fitEFT->next();
                    }
                }

                $fits[] = $fit;
            }

        }

        return $fits;
    }


} 
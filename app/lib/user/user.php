<?php
/**
 * Created by PhpStorm.
 * User: erik
 * Date: 3/13/14
 * Time: 2:11 AM
 */

namespace eveATcheck\lib\user;
use eveATcheck\lib\evefit\lib\fit;
use eveATcheck\lib\evefit\lib\setup;
use eveATcheck\lib\evemodel\evemodel;
use eveATcheck\lib\user\auth\auth;
use eveATcheck\model\setupModel;

/**
 * Class user
 * @package eveATcheck\lib\user
 */
class user
{

    protected $userId;
    protected $userName;
    protected $valid;
    protected $admin;

    protected $model;
    protected $auth;
    protected $loggedin = false;

    public function __construct(evemodel $model, auth $auth)
    {
        $this->model = $model;
        $this->auth  = $auth;

        if (isset($_SESSION['user']))
        {
            $this->loggedin = true;
            $this->userId = $_SESSION['user']['id'];
            $this->userName = $_SESSION['user']['username'];
            $this->valid = $_SESSION['user']['valid'];
            $this->admin = $_SESSION['user']['admin'];
        }
    }

    public function isLoggedin()
    {
        return $this->loggedin;
    }

    public function login($user, $password)
    {
        $login = $this->auth->login($user, $password);
        if (!$login) return false;

        $user = $this->getUser($user);
        if (!$user['valid']) return -2; // this sis a stupid way of doing things

        $_SESSION['user'] = $user;

        $this->loggedin = true;
        $this->userId = $_SESSION['user']['id'];
        $this->userName = $_SESSION['user']['username'];
        $this->valid = $_SESSION['user']['valid'];
        $this->admin = $_SESSION['user']['admin'];

        return true;
    }

    public function logout()
    {
        unset($_SESSION['user']);
    }

    public function register($username, $password)
    {
        return $this->auth->register($username,$password);
    }

    public function getName()
    {
        return $this->userName;
    }

    public function getId()
    {
        return $this->userId;
    }
    public function isAdmin()
    {
	return $this->admin;
    } 

    /**
     * Retrieves fits from the database or session depending on if the user is loggedin
     * @return array
     */
    public function getSetups()
    {
        $setups = array();


        if ($this->loggedin)
        {
            // Get setups
            /** @var setupModel $setupModel */
            $setupModel = $this->model->getModel('setup');
            $setupRows = $setupModel->getSetups();
            foreach ($setupRows as $setupRow)
            {
                $setups[$setupRow['id']] = new setup((int)$setupRow['id'], $setupRow['name'], $setupRow['description'], $setupRow['userId'], $setupRow['username'], $setupRow['publishDate'], $setupRow['updateDate']);
            }

            // Get fits and assign them to the correct setup
            /** @var fitModel $fitModel */
            $fitModel = $this->model->getModel('fit');
            $fitRows = $fitModel->getFits();
            foreach ($fitRows as $fitRow)
            {
                $fit = new fit($fitRow['typeName'], $fitRow['name'], $fitRow['shiptypeId'], $fitRow['groupName'], $this->getId(), $fitRow['description'], $fitRow['id'], $fitRow['publishDate'], $fitRow['updateDate']);
                $fit->setQuantity($fitRow['qty']);
                $fit->parseEFT($fitRow['EFTData'], $this->model);

                if (isset($setups[$fitRow['setupId']]))
                    $setups[$fitRow['setupId']]->addFit($fit);
            }
        }

        return $setups;
    }

    /**
     * Saves fits to session or database depending on if the user is loggedin.
     * @return bool
     */
    public function saveSetups($setups)
    {
        if ($this->loggedin)
        {
            /** @var setupModel $setupModel */
            $setupModel = $this->model->getModel('setup');

            /** @var fitModel $fitModel */
            $fitModel = $this->model->getModel('fit');

            /** @var setup $setup */
            foreach ($setups as $setup)
            {
                if ($setup->isNew())
                {
                    $setupId = $setupModel->insertSetup($setup->getName(), $setup->getDesc(), $this->getId());
                }elseif ($setup->getNeedsSave()) {
                    $setupModel->updateSetup($setup->getId(), $setup->getName(), $setup->getDesc(), $this->getId());
                    $setupId = $setup->getId();
                }else{
                    $setupId = $setup->getId();
                }

                foreach ($setup->getFits() as $fit)
                {
                    if ($fit->isNew())
                    {
                        $fitModel->insertFit($fit->getName(), $fit->getDescription(), $fit->getQuantity(), $fit->getTypeId(), $fit->getEFT(), $this->getId(), $setupId);
                    }elseif ($fit->getNeedsSave()) {
                        $fitModel->updateFit($fit->getId(), $fit->getName(), $fit->getDescription(), $fit->getQuantity(), $fit->getTypeId(), $fit->getEFT(), $this->getId());

                    }
                }

                foreach ($setup->getDeletedFits() as $fitId)
                {
                    $fitModel->deleteFit($fitId);
                }
            }

        }

        return true;
    }

    protected function getUser($user)
    {
        return $this->model->getModel('user')->getUser($user);
    }
} 

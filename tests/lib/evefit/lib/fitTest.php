<?php


use eveATcheck\lib\evefit\lib;

class fitTest extends PHPUnit_Framework_TestCase
{

    protected $fit;
    protected $model;
    protected $itemModel;


    public function setUp()
    {
        $this->fit = $this->getMock('\eveATcheck\lib\evefit\lib\fit', null, array(),'',false);
        $this->model = $this->getMockBuilder('\eveATcheck\lib\evemodel\evemodel')
            ->disableOriginalConstructor()
            ->getMock();

        $this->itemModel = $this->getMockBuilder('\eveATcheck\model\itemModel')
            ->disableOriginalConstructor()
            ->getMock();

        $this->model->expects($this->any())
            ->method('getModel')
            ->will($this->returnValue($this->itemModel));
    }

    public function tearDown()
    {
        unset($this->fit);
    }

    public function testConstruction()
    {
        $fit = new lib\fit('shipType','fitName','typeID','shipGroup',1);
        $fit = new lib\fit('shipType','fitName','typeID','shipGroup',1,'descriont',1,'2014-01-01','2014-01-01');

    }


    public function testparseEFT()
    {
        $moduleMap = array(
            array('Ballistic Control System I',   array('typeName' => 'Ballistic Control System I', 'groupName' => '', 'effectName' => '', 'displayName' => 'low power')),
            array('Micro Auxiliary Power Core I', array('typeName' => 'Micro Auxiliary Power Core I', 'groupName' => '', 'effectName' => '', 'displayName' => 'low power')),
            array('Experimental 1MN Afterburner I', array('typeName' => 'Experimental 1MN Afterburner I', 'groupName' => '', 'effectName' => '', 'displayName' => 'medium power')),
            array('J5 Prototype Warp Disruptor I', array('typeName' => 'J5 Prototype Warp Disruptor I', 'groupName' => '', 'effectName' => '', 'displayName' => 'medium power')),
            array('Medium Azeotropic Ward Salubrity I', array('typeName' => 'Medium Azeotropic Ward Salubrity I', 'groupName' => '', 'effectName' => '', 'displayName' => 'medium power')),
            array('Small Electrochemical Capacitor Booster I', array('typeName' => 'Small Electrochemical Capacitor Booster I', 'groupName' => '', 'effectName' => '', 'displayName' => 'medium power')),
            array('Covert Ops Cloaking Device II', array('typeName' => 'Covert Ops Cloaking Device II', 'groupName' => '', 'effectName' => '', 'displayName' => 'high power')),
        );

        $itemMap = array(
            array('Warrior II',   array('typeName' => 'Warrior II', 'groupName', 'categoryName' => 'Drones')),
            array('Inherent Implants \'Squire\' Engineering EG-601',   array('typeName' => 'Inherent Implants \'Squire\' Engineering EG-601', 'groupName', 'categoryName' => 'Implant')),
            array('Ballistic Control System I',   array('typeName' => 'Ballistic Control System I', 'groupName', 'categoryName' => 'Module')),
            array('Micro Auxiliary Power Core I',   array('typeName' => 'Micro Auxiliary Power Core I', 'groupName', 'categoryName' => 'Module')),
            array('Experimental 1MN Afterburner I',   array('typeName' => 'Experimental 1MN Afterburner I', 'groupName', 'categoryName' => 'Module')),
            array('J5 Prototype Warp Disruptor I',   array('typeName' => 'J5 Prototype Warp Disruptor I', 'groupName', 'categoryName' => 'Module')),
            array('Medium Azeotropic Ward Salubrity I',   array('typeName' => 'Medium Azeotropic Ward Salubrity I', 'groupName', 'categoryName' => 'Module')),
            array('Small Electrochemical Capacitor Booster I',   array('typeName' => 'Small Electrochemical Capacitor Booster I', 'groupName', 'categoryName' => 'Module')),
            array('Covert Ops Cloaking Device II',   array('typeName' => 'Covert Ops Cloaking Device II', 'groupName', 'categoryName' => 'Module')),
            array('Navy Cap Booster 50',   array('typeName' => 'Navy Cap Booster 50', 'groupName', 'categoryName' => 'Charge')),


        );

        $testFit = <<<FIT
[Nemesis, test's Nemesis]

Ballistic Control System I
Micro Auxiliary Power Core I

Experimental 1MN Afterburner I
J5 Prototype Warp Disruptor I
Medium Azeotropic Ward Salubrity I
Small Electrochemical Capacitor Booster I, Navy Cap Booster 50

Covert Ops Cloaking Device II


Warrior II x5


Inherent Implants 'Squire' Engineering EG-601
FIT;

        $this->itemModel->expects($this->any())
            ->method('getModule')
            ->will($this->returnValueMap($moduleMap));

        $this->itemModel->expects($this->any())
            ->method('getItem')
            ->will($this->returnValueMap($itemMap));


        $this->fit->parseEFT($testFit, $this->model);


        $result = $modules = $this->fit->getEFT();

        $this->assertEquals(trim($testFit), trim($result));
    }

}

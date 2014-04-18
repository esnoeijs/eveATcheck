<?php


use eveATcheck\lib\evemodel;
use eveATcheck\lib\user;
use eveATcheck\lib\evefit;

class evefitTest extends PHPUnit_Framework_TestCase
{

    protected $eveFit;
    protected $model;
    protected $user;

    public function setUp()
    {
        $this->model = $this->getMock('\eveATcheck\lib\evemodel\evemodel',null,array(),'',false);
        $this->user  = $this->getMockBuilder('\eveATcheck\lib\user\user')
            ->disableOriginalConstructor()
            ->getMock();

        $this->eveFit = new evefit\evefit($this->model, $this->user);
    }

    public function tearDown()
    {
        unset($this->model);
        unset($this->user);
        unset($this->eveFit);
    }

    public function testLoadSetups()
    {
        $this->user
            ->expects($this->once())
            ->method('getSetups')
            ->will($this->returnValue(array('TEST')));

        $class = new ReflectionClass($this->eveFit);
        $method = $class->getMethod('loadSetups');
        $method->setAccessible(true);

        $method->invokeArgs($this->eveFit, array());

        $this->assertEquals(array('TEST'), $this->eveFit->getSetups());
    }

    public function testAddSetup()
    {
        $this->user->expects($this->once())->method('saveSetups');
        $this->eveFit = new evefit\evefit($this->model, $this->user);

        $this->eveFit->addSetup('TEST', false);
        $this->assertEquals(array('TEST'), $this->eveFit->getSetups());

        $this->eveFit->addSetup('TEST2');
        $this->assertEquals(array('TEST','TEST2'), $this->eveFit->getSetups());
    }

    public function testParseEFT()
    {
        $testFit = <<<FIT
[Nemesis, hank's Nemesis]
Ballistic Control System I
Micro Auxiliary Power Core I

Experimental 1MN Afterburner I
J5 Prototype Warp Disruptor I
Medium Azeotropic Ward Salubrity I
Small Electrochemical Capacitor Booster I,Cap Booster 150

Covert Ops Cloaking Device II
FIT;

        $mockFit = $this->getMockBuilder('\eveATcheck\lib\evefit\lib\fit')
            ->disableOriginalConstructor()
            ->getMock();

        $eveFit = $this->getMockBuilder('\eveATcheck\lib\evefit\evefit')
                    ->setMethods(array('getNewFit'))
                    ->setConstructorArgs(array($this->model, $this->user))
                    ->getMock();

        $eveFit->expects($this->any())->method('getNewFit')->will($this->returnValue($mockFit));

        $class = new ReflectionClass($eveFit);
        $method = $class->getMethod('parseEFT');
        $method->setAccessible(true);

        $fits = $method->invokeArgs($eveFit, array($testFit));

        $this->assertTrue(is_array($fits));
        $this->assertTrue(count($fits)==1);

        $this->assertInstanceOf('\eveATcheck\lib\evefit\lib\fit', array_shift($fits));
    }

}

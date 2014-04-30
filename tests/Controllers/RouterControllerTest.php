<?php
require_once __DIR__."/../../Settings.class.php";
require_once __DIR__."/../../Controllers/Controller.php";
require_once __DIR__."/../../Controllers/RouterController.php";
/**
 * Created by PhpStorm.
 * User: Ratan
 * Date: 28.4.14
 * Time: 12:43
 */

class RouterControllerTest extends PHPUnit_Framework_TestCase {
    protected $controller;

    protected function setUp(){
        $this->controller= new RouterController();
    }

    public function testParse(){
        $this->assertEquals(array("para1", "para2"), $this->controller->parse("/DMP/para1/para2"));
    }
}
 
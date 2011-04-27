<?php
require_once 'Image/Image.php';

class ImageTest extends PHPUnit_Framework_TestCase
{
    protected function setup()
    {}
    
    /**
     * Teardown after each unit test - part of test case API
     */
    protected function tearDown()
    {

    }
    
    public function testDef()
    {
        $image = new Image_Image('sheep.jpg');
        
        $this->assertType('object', $image, 'Invalid type');
    }
}

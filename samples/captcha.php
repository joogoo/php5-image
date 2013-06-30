<?php
require_once dirname(__FILE__) . '/bootstrap.php';

$image = new Image_Image();

$image->createImageTrueColor(206, 96, "FF0000");

//Primitives
$background = new Image_Draw_Primitive("FFFFFF");
$background->addLine(20,20, 80,80);
$background->addRectangle(100,20, 180,80);
$background->addFilledRectangle(150,10, 170,30);

$background->addEllipse(10,50, 20,60);
$background->addFilledEllipse(140,60, 160,80);

$background->addCircle(200,50,30);

$image->attach($background);

//Captcha text
$captcha = new Image_Draw_Captcha("captcha");

$captcha->addTTFFont(dirname(__FILE__) . '/../fonts/blambotcustom.ttf');
$captcha->addTTFFont(dirname(__FILE__) . '/../fonts/adventure.ttf');
$captcha->addTTFFont(dirname(__FILE__) . '/../fonts/bluehigh.ttf');

$captcha->setTextSize(20)
        ->setSizeRandom(20)
        ->setAngleRandom(60)
        ->setTextSpacing(5);

$image->attach($captcha);

//Add a border
$image->attach(new Image_Draw_Border(1, "BBBBBB"));
$image->attach(new Image_Draw_Border(1, "FFFFFF"));

$image->imagePng();

<?php
require_once dirname(__FILE__) . '/bootstrap.php';

$image = new Image_Image(dirname(__FILE__) . '/source/rose.jpg');

$image->attach(new Image_Fx_Resize(198));
$image->attach(new Image_Fx_Crop(196,96));

$secondImage = new Image_Image(dirname(__FILE__) . '/source/cherry.png');

$layer = new Image_Draw_Layer($secondImage);

$image->attach($layer);

$image->imagePng();

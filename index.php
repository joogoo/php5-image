<?php


    echo "<html>";
    echo "<head>";
    echo "<style>";
    echo ".thumbHolder { float: left; width: 250px; height: 170px; margin-right: 10px; margin-bottom: 5px; }\n";
    echo ".thumb { width: 250px; margin: 25px 0px; text-align: center; }\n";
    echo ".caption { width: 250px; height: 20px; margin: 0px; text-align: center; font-family: Verdana, Sans Serif; font-size: 0.8em; background-color: #FFF }\n";
    echo "</style>";
    echo "</head>";
    echo "<body>";

    echo "<div style=\"width: 1040px; margin: 0px auto;\">";

        echo "<div class=\"thumbHolder\" style=\"background: url('samples/bg/11.gif');\">";
            echo "<div class=\"thumb\"><img src=\"samples/vanilla.php\"></div>";
            echo "<div class=\"caption\">Vanilla</div>";
        echo "</div>";
        
        echo "<div class=\"thumbHolder\" style=\"background: url('samples/bg/16.gif');\">";
            echo "<div class=\"thumb\"><img src=\"samples/watermark.php\"></div>";
            echo "<div class=\"caption\">Watermark</div>";
        echo "</div>";

        echo "<div class=\"thumbHolder\" style=\"background: url('samples/bg/4.gif');\">";
            echo "<div class=\"thumb\"><img src=\"samples/canvassize.php\"></div>";
            echo "<div class=\"caption\">Canvassize + Watermark</div>";
        echo "</div>";

        echo "<div class=\"thumbHolder\" style=\"background: url('samples/bg/6.gif');\">";
            echo "<div class=\"thumb\"><img src=\"samples/tile.php\"></div>";
            echo "<div class=\"caption\">Tiled Watermark</div>";
        echo "</div>";

        echo "<div class=\"thumbHolder\" style=\"background: url('samples/bg/13.gif');\">";
            echo "<div class=\"thumb\"><img src=\"samples/square_border.php\"></div>";
            echo "<div class=\"caption\">Black and White</div>";
        echo "</div>";

        echo "<div class=\"thumbHolder\" style=\"background: url('samples/bg/12.gif');\">";
            echo "<div class=\"thumb\"><img src=\"samples/infobar.php\"></div>";
            echo "<div class=\"caption\">Information Bar</div>";
        echo "</div>";

        echo "<div class=\"thumbHolder\" style=\"background: url('samples/bg/9.gif');\">";
            echo "<div class=\"thumb\"><img src=\"samples/red_border.php\"></div>";
            echo "<div class=\"caption\">Border + Corners</div>";
        echo "</div>";

        echo "<div class=\"thumbHolder\" style=\"background: url('samples/bg/10.gif');\">";
            echo "<div class=\"thumb\"><img src=\"samples/vignette.php\"></div>";
            echo "<div class=\"caption\">Vignette</div>";
        echo "</div>";

        echo "<div class=\"thumbHolder\" style=\"background: url('samples/bg/3.gif');\">";
            echo "<div class=\"thumb\"><img src=\"samples/trueshadow.php\"></div>";
            echo "<div class=\"caption\">Drop Shadow</div>";
        echo "</div>";

        echo "<div class=\"thumbHolder\" style=\"background: url('samples/bg/1.gif');\">";
            echo "<div class=\"thumb\"><img src=\"samples/scanline.php\"></div>";
            echo "<div class=\"caption\">Scanlines</div>";
        echo "</div>";

        echo "<div class=\"thumbHolder\" style=\"background: url('samples/bg/5.gif');\">";
            echo "<div class=\"thumb\"><img src=\"samples/gaussian.php\"></div>";
            echo "<div class=\"caption\">Gaussian Blur</div>";
        echo "</div>";

        echo "<div class=\"thumbHolder\" style=\"background: url('samples/bg/14.gif');\">";
            echo "<div class=\"thumb\"><img src=\"samples/jitter.php\"></div>";
            echo "<div class=\"caption\">Jitter</div>";
        echo "</div>";

        echo "<div class=\"thumbHolder\" style=\"background: url('samples/bg/7.gif');\">";
            echo "<div class=\"thumb\"><img src=\"samples/ripple.php\"></div>";
            echo "<div class=\"caption\">Ripple</div>";
        echo "</div>";

        echo "<div class=\"thumbHolder\" style=\"background: url('samples/bg/8.gif');\">";
            echo "<div class=\"thumb\"><img src=\"samples/sobel.php\"></div>";
            echo "<div class=\"caption\">Sobel Edge Detection</div>";
        echo "</div>";

        echo "<div class=\"thumbHolder\" style=\"background: url('samples/bg/2.gif');\">";
            echo "<div class=\"thumb\"><img src=\"samples/filter.php\"></div>";
            echo "<div class=\"caption\">Filter</div>";
        echo "</div>";

        echo "<div class=\"thumbHolder\" style=\"background: url('samples/bg/15.gif');\">";
            echo "<div class=\"thumb\"><img src=\"samples/captcha.php\"></div>";
            echo "<div class=\"caption\">Captcha</div>";
        echo "</div>";

        echo "<div class=\"thumbHolder\" style=\"background: url('samples/bg/1.gif');\">";
            echo "<div class=\"thumb\"><img src=\"samples/colorize.php\"></div>";
            echo "<div class=\"caption\">Colorize</div>";
        echo "</div>";

        echo "<div class=\"thumbHolder\" style=\"background: url('samples/bg/2.gif');\">";
            echo "<div class=\"thumb\"><img src=\"samples/psd.php\"></div>";
            echo "<div class=\"caption\">PSD Parser</div>";
        echo "</div>";

        echo "<div class=\"thumbHolder\" style=\"background: url('samples/bg/3.gif');\">";
            echo "<div class=\"thumb\"><img src=\"samples/ico.php\"></div>";
            echo "<div class=\"caption\">ICO Parser</div>";
        echo "</div>";

        echo "<div class=\"thumbHolder\" style=\"background: #FFFFFF\">";
            echo "<div class=\"thumb\"><img src=\"samples/qrcode.php\"></div>";
            echo "<div class=\"caption\">QR Code</div>";
        echo "</div>";

        echo "<div class=\"thumbHolder\" style=\"background: url('samples/bg/10.gif');\">";
            echo "<div class=\"thumb\"><img src=\"samples/layers.php\"></div>";
            echo "<div class=\"caption\">Layers</div>";
        echo "</div>";
        
    echo "</div>";
    
    echo "</body>";
    echo "</html>";

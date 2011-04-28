<?php 

class Image_Colour
{
    private $__alpha = 0;
    
    private $__red = 0;
    
    private $__green = 0;
    
    private $__blue = 0;
    
    public function __construct($hexColor = '000000')
    {
        
    }
    
    public function __toString()
    {
        return "";
    }
    
    public function getAlpha()
    {
        return $this->__alpha;
    }
    
    public function getRed()
    {
        return $this->__red;
    }
    
    public function getGreen()
    {
        return $this->__green;
    }
    
    public function getBlue()
    {
        return $this->__blue;
    }
    
    public function getIntColor()
    {
        $intColor = (($this->__alpha & 0xFF) << 24) | (($this->__red & 0xFF) << 16) |
         (($this->__green & 0xFF) << 8) | (($this->__blue & 0xFF) << 0);
        return $intColor;  
    }
    
    public function setIntColor($intColor = 0)
    {
        $this->__alpha = ($intColor >> 24) & 0xFF;
        $this->__red = ($intColor >> 16) & 0xFF;
        $this->__green = ($intColor >> 8) & 0xFF;
        $this->__blue = ($intColor) & 0xFF;
        
        return $this;
    }
    
    public function setHexColor($hexColor = '000000')
    {
        $this->__red = hexdec(substr($hexColor, 0, 2));
        $this->__green = hexdec(substr($hexColor, 2, 2));
        $this->__blue = hexdec(substr($hexColor, 4, 2));
        
        return $this;
    }


    public static function intColorToHexColor($intColor = 0)
    {
        $arrColor = self::intColorToArrayColor($intColor);
        $hexColor = self::arrayColorToHexColor($arrColor);
        return $hexColor;
    }

    public static function hexColorToArrayColor($hexColor = "000000")
    {
        $arrColor['red'] = hexdec(substr($hexColor, 0, 2));
        $arrColor['green'] = hexdec(substr($hexColor, 2, 2));
        $arrColor['blue'] = hexdec(substr($hexColor, 4, 2));
        return $arrColor;
    }

    public static function hexColorToIntColor($hexColor = "000000")
    {
        $arrColor = self::hexColorToArrayColor($hexColor);
        $intColor = self::arrayColorToIntColor($arrColor);
        return $intColor;
    }
}
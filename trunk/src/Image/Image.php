<?php

/**
 * image-image
 *
 * Copyright (c) 2009-2011, Nikolay Petrovski <to.petrovski@gmail.com>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Sebastian Bergmann nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package   Image
 * @author    Nikolay Petrovski <to.petrovski@gmail.com>
 * @copyright 2009-2011 Nikolay Petrovski <to.petrovski@gmail.com>
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @since     File available since Release 1.0.0
 */
require_once 'Image/Exception.php';

require_once 'Image/Plugin/Interface.php';

class Image_Image {

    public $image;
    public $mid_handle = true; //Set as false to use the top left corner as the handle.
    protected $_settings = array();
    protected $_attachments = array();
    protected $_attachments_stack = array();
    protected $_reader;

    public function __construct() {

        $args = func_get_args();

        switch (count($args)) {
            case 1:
                if (!empty($args[0]))
                    $this->openImage($args[0]);
                break;
            case 2:
                $this->createImageTrueColor($args[0], $args[1]);
                break;
        }
    }

    public function attach(Image_Plugin_Interface $child) {
        $type = $child->getTypeId();

        if (array_key_exists($type, $this->_attachments)) {
            $this->_attachments[$type]++;
        } else {
            $this->_attachments[$type] = 1;
        }
        $id = "#" . $type . "_" . $this->_attachments[$type];
        $this->_attachments_stack[$id] = $child;
        $this->_attachments_stack[$id]->attachToOwner($this);
        return $id;
    }

    public function evaluateFXStack() {

        foreach ($this->_attachments_stack as $attachment) {
            if ($attachment instanceof Image_Plugin_Interface) {
                $attachment->generate();
            }
        }

        return true;
    }

    public function createImage($width = 100, $height = 100, $color = "FFFFFF") {
        $this->image = imagecreate($width, $height);
        if (!empty($color)) {
            $this->imagefill(0, 0, $color);
        }
    }

    public function createImageTrueColor($width = 100, $height = 100, $color = "FFFFFF") {
        $this->image = imagecreatetruecolor($width, $height);
        if (!empty($color)) {
            $this->imagefill(0, 0, $color);
        }
    }

    public function createImageTrueColorTransparent($x = 100, $y = 100) {
        $this->image = imagecreatetruecolor($x, $y);
        $blank = imagecreatefromstring(base64_decode($this->_blankpng()));
        imagesavealpha($this->image, true);
        imagealphablending($this->image, false);
        imagecopyresized($this->image, $blank, 0, 0, 0, 0, $x, $y, imagesx($blank), imagesy($blank));
        imagedestroy($blank);
    }

    public function openImage($filename = "") {
        if (file_exists($filename)) {

            require_once 'Image/Reader/Default.php';

            $this->_reader = new Image_Reader_Default($filename);

            $this->image = $this->_reader->read($filename);

            if ('resource' != gettype($this->image)) {
                unset($this->image);
            }

            $this->_file_info($filename);
        } else {
            //file does not exist
            return false;
        }
    }

    public function printImage($type, $filename = "") {
        if (!isset($this->image)) {
            return false;
        }
        $this->evaluateFXStack();

        $gd_function = 'image' . strtolower($type);
        if (function_exists($gd_function)) {
            if (!empty($filename)) {
                return call_user_func($gd_function, $this->image, $filename);
            } else {
                header("Content-type: " . image_type_to_mime_type(constant('IMAGETYPE_' . strtoupper($type))));
                return call_user_func($gd_function, $this->image);
            }
        }
    }

    public function destroyImage() {
        if (!isset($this->image)) {
            return false;
        }
        imagedestroy($this->image);
        unset($this->image);
    }

    public function imagesx() {
        if (!isset($this->image)) {
            return false;
        }
        return imagesx($this->image);
    }

    public function imagesy() {
        if (!isset($this->image)) {
            return false;
        }
        return imagesy($this->image);
    }

    public function imageIsTrueColor() {
        if (!isset($this->image)) {
            return false;
        }
        return imageistruecolor($this->image);
    }

    /**
     * 
     * @param int $x
     * @param int $y
     * @return int
     */
    public function imageColorAt($x = 0, $y = 0) {
        if (!isset($this->image)) {
            return false;
        }
        $color = imagecolorat($this->image, $x, $y);
        if (!$this->imageIsTrueColor()) {
            $arrColor = imagecolorsforindex($this->image, $color);
            return $this->arrayColorToIntColor($arrColor);
        } else {
            return $color;
        }
    }

    public function imagefill($x = 0, $y = 0, $color = "FFFFFF", $alpha = null) {
        if (!isset($this->image)) {
            return false;
        }

        imagefill($this->image, $x, $y, $this->imagecolorallocate($color, $alpha));
    }

    public function imagecolorallocate($color = "FFFFFF", $alpha = null) {
        
        if (is_string($color)) {
            $arrColor = self::hexColorToArrayColor($color);
        } else if (is_int($color)) {
            $arrColor = self::intColorToArrayColor($color);
            $alpha = $arrColor['alpha'];
        }
        
        if ($alpha) {
            return imagecolorallocate($this->image, $arrColor['red'], $arrColor['green'], $arrColor['blue']);
        } else {
            return imagecolorallocatealpha($this->image, $arrColor['red'], $arrColor['green'], $arrColor['blue'], intval($alpha));
        }
    }

    public function displace($map) {
        $width = $this->imagesx();
        $height = $this->imagesy();
        $temp = new self($width, $height);
        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                $rgb = $this->imageColorAt($map['x'][$x][$y], $map['y'][$x][$y]);
                imagesetpixel($temp->image, $x, $y, $this->imagecolorallocate($rgb));
            }
        }
        $this->image = $temp->image;
        return true;
    }

    public function testImageHandle() {
        return (bool) (isset($this->image) && 'gd' == get_resource_type($this->image));
    }

    public function __get($name) {
        if ($name == "image") {
            return $this->image;
        }
        if ($name == "handle_x") {
            return ($this->mid_handle == true) ? floor($this->imagesx() / 2) : 0;
        }
        if ($name == "handle_y") {
            return ($this->mid_handle == true) ? floor($this->imagesy() / 2) : 0;
        }
        if ('#' == $name{0}) {
            return $this->_attachments_stack[$name];
        } elseif (array_key_exists($name, $this->_settings)) {
            return $this->_settings[$name];
        } else {
            return false;
        }
    }

    public function __set($name, $value) {
        if ($name == "image") {
            $this->image = $value;
        } elseif ('#' == $name{0}) {
            $this->_attachments_stack[$name] = $value;
        } else {
            $this->_settings[$name] = $value;
        }
    }

    public function __call($name, $arguments) {
        if (substr($name, 0, 5) == 'image') {
            return $this->printImage(substr($name, 5, strlen($name) - 5), empty($arguments) ? '' : $arguments[0]);
        }
    }

    private function _file_info($filename, $round = 2) {
        $ext = array('B', 'KB', 'MB', 'GB');

        $this->filepath = $filename;
        $this->filename = basename($filename);
        $this->filesize_bytes = filesize($filename);
        $size = $this->filesize_bytes;
        for ($i = 0; $size > 1024 && $i < count($ext) - 1; $i++) {
            $size /= 1024;
        }
        $this->filesize_formatted = round($size, $round) . $ext[$i];
        $this->original_width = $this->imagesx();
        $this->original_height = $this->imagesy();
    }

    private function _blankpng() {
        return <<<BLACKPNG
iVBORw0KGgoAAAANSUhEUgAAACgAAAAoCAYAAACM/rhtAAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29m
dHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAADqSURBVHjaYvz//z/DYAYAAcTEMMgBQAANegcCBNCg
dyBAAA16BwIE0KB3IEAADXoHAgTQoHcgQAANegcCBNCgdyBAAA16BwIE0KB3IEAADXoHAgTQoHcgQAAN
egcCBNCgdyBAAA16BwIE0KB3IEAADXoHAgTQoHcgQAANegcCBNCgdyBAAA16BwIE0KB3IEAADXoHAgTQ
oHcgQAANegcCBNCgdyBAAA16BwIE0KB3IEAADXoHAgTQoHcgQAANegcCBNCgdyBAAA16BwIE0KB3IEAA
DXoHAgTQoHcgQAANegcCBNCgdyBAgAEAMpcDTTQWJVEAAAAASUVORK5CYII=
BLACKPNG;
    }
    
    
    /*** Color Helper Static Methods **/
        
    /**
     * Array => Hex
     * 
     * @param array $arrColor
     * @return string
     */
    public static function arrayColorToHexColor($arrColor = array(0, 0, 0)) {
        return self::intColorToHexColor(self::arrayColorToIntColor($arrColor));
    }
    
    /**
     * Int => Hex
     * 
     * @param int $intColor
     * @return string
     */
    public static function intColorToHexColor($intColor = 0) {
        return str_pad(dechex($intColor), 6, "0", STR_PAD_LEFT);
    }
    
    /**
     * Hex => Int
     * 
     * @param string $hexColor
     * @return int
     */
    public static function hexColorToIntColor($hexColor = "000000") {
        return self::arrayColorToIntColor(self::hexColorToArrayColor($hexColor));
    }
    
    /**
     * Int => Array
     * 
     * @param int $intColor
     * @return array
     */
    public static function intColorToArrayColor($intColor = 0) {
        return array(
            'alpha' => ($intColor >> 24) & 0xFF,
            'red' => ($intColor >> 16) & 0xFF,
            'green' => ($intColor >> 8) & 0xFF,
            'blue' => ($intColor) & 0xFF
        );
    }

    /**
     * Hex => Array
     * 
     * @param string $hexColor
     * @return array
     */
    public static function hexColorToArrayColor($hexColor = "000000") {
        return array(
            'alpha' => null,
            'red' => hexdec(substr($hexColor, 0, 2)),
            'green' => hexdec(substr($hexColor, 2, 2)),
            'blue' => hexdec(substr($hexColor, 4, 2))
        );
    }
    
    /**
     * Array => Int
     * 
     * @param array $arrColor
     * @return int
     */
    public static function arrayColorToIntColor($arrColor = array(0, 0, 0)) {
        return (($arrColor['alpha'] & 0xFF) << 24) | 
               (($arrColor['red'] & 0xFF) << 16) |
               (($arrColor['green'] & 0xFF) << 8) | 
               (($arrColor['blue'] & 0xFF) << 0);
    }
    
    /**
     * Get current color in XYZ format
     * 
     * @param array $arrColor
     * @return array
     */
    public static function arrayColorToXyz($arrColor = array(0, 0, 0)) {

        // Normalize RGB values to 1
        $arrColor = array_map(create_function('$item', 'return $item / 255;'), $arrColor);

        $arrColor = array_map(create_function('$item', '$item = ($item > 0.04045) ? pow((($item + 0.055) / 1.055), 2.4) : $item / 12.92; return ($item * 100);'), $arrColor);

        //Observer. = 2°, Illuminant = D65
        $xyz = array(
            'x' => ($arrColor['red'] * 0.4124) + ($arrColor['green'] * 0.3576) + ($arrColor['blue'] * 0.1805),
            'y' => ($arrColor['red'] * 0.2126) + ($arrColor['green'] * 0.7152) + ($arrColor['blue'] * 0.0722),
            'z' => ($arrColor['red'] * 0.0193) + ($arrColor['green'] * 0.1192) + ($arrColor['blue'] * 0.9505)
        );

        return $xyz;
    }

    /**
     * Get color CIE-Lab values
     * 
     * @param array $arrColor
     * @return array
     */
    public static function arraColorToLabCie($arrColor = array(0, 0, 0)) {
        
        $xyz = self::arrayColorToXyz($arrColor);

        //Ovserver = 2*, Iluminant=D65
        $xyz['x'] /= 95.047;
        $xyz['y'] /= 100;
        $xyz['z'] /= 108.883;

        $xyz = array_map(create_function('$item', 'return ($item > 0.008856) ? pow($item, 1 / 3) : (7.787 * $item) + (16 / 116);'), $xyz);

        return array(
            'l' => (116 * $xyz['y']) - 16,
            'a' => 500 * ($xyz['x'] - $xyz['y']),
            'b' => 200 * ($xyz['y'] - $xyz['z'])
        );
    }
    
    /**
     * Get HSV values for color
     * (integer values from 0-255, fast but less accurate)
     * 
     * @param array $arrColor
     * @return int 
     */
    public static function arrayColorToHsvInt($arrColor = array(0, 0, 0)) {

        $rgbMin = min($arrColor);
        $rgbMax = max($arrColor);

        $hsv = array(
            'hue' => 0,
            'sat' => 0,
            'val' => $rgbMax
        );

        // If value is 0, color is black
        if ($hsv['val'] == 0) {
            return $hsv;
        }

        // Calculate saturation
        $hsv['sat'] = round(255 * ($rgbMax - $rgbMin) / $hsv['val']);
        if ($hsv['sat'] == 0) {
            $hsv['hue'] = 0;
            return $hsv;
        }

        // Calculate hue
        if ($rgbMax == $arrColor['red']) {
            $hsv['hue'] = round(0 + 43 * ($arrColor['green'] - $arrColor['blue']) / ($rgbMax - $rgbMin));
        } else if ($rgbMax == $arrColor['green']) {
            $hsv['hue'] = round(85 + 43 * ($arrColor['blue'] - $arrColor['red']) / ($rgbMax - $rgbMin));
        } else {
            $hsv['hue'] = round(171 + 43 * ($arrColor['red'] - $arrColor['green']) / ($rgbMax - $rgbMin));
        }
        if ($hsv['hue'] < 0) {
            $hsv['hue'] += 255;
        }

        return $hsv;
    }
}


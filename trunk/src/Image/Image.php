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
        $id = "a_" . $type . "_" . $this->_attachments[$type];
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

    public function imagefill($x = 0, $y = 0, $color = "FFFFFF") {
        if (!isset($this->image)) {
            return false;
        }
        $arrColor = Image_Image::hexColorToArrayColor($color);
        $bgcolor = imagecolorallocate($this->image, $arrColor['red'], $arrColor['green'], $arrColor['blue']);
        imagefill($this->image, $x, $y, $bgcolor);
    }

    public function imagecolorallocate($color = "FFFFFF") {
        $arrColor = Image_Image::hexColorToArrayColor($color);
        return imagecolorallocate($this->image, $arrColor['red'], $arrColor['green'], $arrColor['blue']);
    }

    public function displace($map) {
        $width = $this->imagesx();
        $height = $this->imagesy();
        $temp = new Image_Image($width, $height);
        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                $rgb = $this->imageColorAt($map['x'][$x][$y], $map['y'][$x][$y]);
                $arrRgb = Image_Image::intColorToArrayColor($rgb);
                $col = imagecolorallocatealpha($temp->image, $arrRgb['red'], $arrRgb['green'], $arrRgb['blue'], $arrRgb['alpha']);
                imagesetpixel($temp->image, $x, $y, $col);
            }
        }
        $this->image = $temp->image;
        return true;
    }

    public function testImageHandle() {
        return (bool) (isset($this->image) && 'gd' == get_resource_type($this->image));
    }

    public static function arrayColorToIntColor($arrColor = array(0, 0, 0)) {
        $intColor = (($arrColor['alpha'] & 0xFF) << 24) | (($arrColor['red'] & 0xFF) << 16) |
                (($arrColor['green'] & 0xFF) << 8) | (($arrColor['blue'] & 0xFF) << 0);
        return $intColor;
    }

    public static function arrayColorToHexColor($arrColor = array(0, 0, 0)) {
        $intColor = Image_Image::arrayColorToIntColor($arrColor);
        $hexColor = Image_Image::intColorToHexColor($intColor);
        return $hexColor;
    }

    public static function intColorToArrayColor($intColor = 0) {
        $arrColor['alpha'] = ($intColor >> 24) & 0xFF;
        $arrColor['red'] = ($intColor >> 16) & 0xFF;
        $arrColor['green'] = ($intColor >> 8) & 0xFF;
        $arrColor['blue'] = ($intColor) & 0xFF;
        return $arrColor;
    }

    public static function intColorToHexColor($intColor = 0) {
        $arrColor = Image_Image::intColorToArrayColor($intColor);
        $hexColor = Image_Image::arrayColorToHexColor($arrColor);
        return $hexColor;
    }

    public static function hexColorToArrayColor($hexColor = "000000") {
        $arrColor['red'] = hexdec(substr($hexColor, 0, 2));
        $arrColor['green'] = hexdec(substr($hexColor, 2, 2));
        $arrColor['blue'] = hexdec(substr($hexColor, 4, 2));
        return $arrColor;
    }

    public static function hexColorToIntColor($hexColor = "000000") {
        $arrColor = Image_Image::hexColorToArrayColor($hexColor);
        $intColor = Image_Image::arrayColorToIntColor($arrColor);
        return $intColor;
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
        if (substr($name, 0, 2) == "a_") {
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
        } elseif (substr($name, 0, 2) == "a_") {
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

    private function _file_info($filename) {
        $ext = array(
            'B', 'KB', 'MB', 'GB'
        );

        $round = 2;
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

}

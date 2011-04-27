<?php
/**
 * image-draw-primitive
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


class Image_Draw_Primitive extends Image_Plugin_Base implements Image_Plugin_Interface {

    public $type_id = "draw";

    public $sub_type_id = "primitive";

    public $version = 1.0;

    private $__shapes = array();

    public function __construct($base_color = "000000")
    {
        $this->base_color = $base_color;
    }

    public function addLine($x1, $y1, $x2, $y2, $color = "")
    {
        if(empty($color)) {
            $color = $this->base_color;
        }
        $this->__shapes[] = array(
            "LINE", $x1, $y1, $x2, $y2, $color
        );
    }

    public function addRectangle($x1, $y1, $x2, $y2, $color = "", $filled = false)
    {
        if(empty($color)) {
            $color = $this->base_color;
        }
        if(! $filled) {
            $this->__shapes[] = array(
                "RECTANGLE", $x1, $y1, $x2, $y2,
            $color
            );
        }
        else {
            $this->__shapes[] = array(
                "FILLED_RECTANGLE", $x1, $y1, $x2,
            $y2, $color
            );
        }
    }

    public function addFilledRectangle($x1, $y1, $x2, $y2, $color = "")
    {
        if(empty($color)) {
            $color = $this->base_color;
        }
        $this->__shapes[] = array(
            "FILLED_RECTANGLE", $x1, $y1, $x2, $y2, $color
        );
    }

    public function addEllipse($x1, $y1, $x2, $y2, $color = "", $filled = false)
    {
        if(empty($color)) {
            $color = $this->base_color;
        }
        $w = $x2 - $x1;
        $h = $y2 - $y1;
        if(! $filled) {
            $this->__shapes[] = array(
                "ELLIPSE", $x1, $y1, $w, $h, $color
            );
        }
        else {
            $this->__shapes[] = array(
                "FILLED_ELLIPSE", $x1, $y1, $w, $h,
            $color
            );
        }
    }

    public function addFilledEllipse($x1, $y1, $x2, $y2, $color = "")
    {
        if(empty($color)) {
            $color = $this->base_color;
        }
        $w = $x2 - $x1;
        $h = $y2 - $y1;
        $this->__shapes[] = array(
            "FILLED_ELLIPSE", $x1, $y1, $w, $h, $color
        );
    }

    public function addCircle($x, $y, $r, $color = "")
    {
        if(empty($color)) {
            $color = $this->base_color;
        }
        $this->__shapes[] = array(
            "ELLIPSE", $x, $y, $r, $r, $color
        );
    }

    public function generate()
    {
        foreach($this->__shapes as $shape) {
            switch ($shape[0]) {
                case "LINE":
                    $color = $this->_owner->imagecolorallocate($shape[5]);
                    imageline($this->_owner->image, $shape[1], $shape[2], $shape[3], $shape[4], $color);
                    break;
                case "RECTANGLE":
                    $color = $this->_owner->imagecolorallocate($shape[5]);
                    imagerectangle($this->_owner->image, $shape[1], $shape[2], $shape[3], $shape[4], $color);
                    break;
                case "FILLED_RECTANGLE":
                    $color = $this->_owner->imagecolorallocate($shape[5]);
                    imagefilledrectangle($this->_owner->image, $shape[1], $shape[2], $shape[3], $shape[4], $color);
                    break;
                case "ELLIPSE":
                    $color = $this->_owner->imagecolorallocate($shape[5]);
                    imageellipse($this->_owner->image, $shape[1], $shape[2], $shape[3], $shape[4], $color);
                    break;
                case "FILLED_ELLIPSE":
                    $color = $this->_owner->imagecolorallocate($shape[5]);
                    imagefilledellipse($this->_owner->image, $shape[1], $shape[2], $shape[3], $shape[4], $color);
                    break;
            }
        }
    }
}

<?php
/**
 * image-fx-filter
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


class Image_Fx_Filter extends Image_Plugin_Base implements Image_Plugin_Interface {

    public $type_id = "effect";

    public $sub_type_id = "filter";

    public $version = 1.0;

    public function __construct($filter = IMG_FILTER_NEGATE, $arg1 = 0, $arg2 = 0, $arg3 = 0, $arg4 = 0)
    {
        $this->filter = $filter;
        $this->arg1 = $arg1;
        $this->arg2 = $arg2;
        $this->arg3 = $arg3;
    }

    public function generate()
    {
        switch ($this->filter) {
            case IMG_FILTER_NEGATE:
                imagefilter($this->_owner->image, IMG_FILTER_NEGATE);
                break;
            case IMG_FILTER_GRAYSCALE:
                imagefilter($this->_owner->image, IMG_FILTER_GRAYSCALE);
                break;
            case IMG_FILTER_BRIGHTNESS:
                imagefilter($this->_owner->image, IMG_FILTER_BRIGHTNESS, $this->arg1);
                break;
            case IMG_FILTER_CONTRAST:
                imagefilter($this->_owner->image, IMG_FILTER_CONTRAST, $this->arg1);
                break;
            case IMG_FILTER_COLORIZE:
                imagefilter($this->_owner->image, IMG_FILTER_COLORIZE, $this->arg1, $this->arg2, $this->arg3, $this->arg3);
                break;
            case IMG_FILTER_EDGEDETECT:
                imagefilter($this->_owner->image, IMG_FILTER_EDGEDETECT);
                break;
            case IMG_FILTER_EMBOSS:
                imagefilter($this->_owner->image, IMG_FILTER_EMBOSS);
                break;
            case IMG_FILTER_GAUSSIAN_BLUR:
                imagefilter($this->_owner->image, IMG_FILTER_GAUSSIAN_BLUR);
                break;
            case IMG_FILTER_SELECTIVE_BLUR:
                imagefilter($this->_owner->image, IMG_FILTER_SELECTIVE_BLUR);
                break;
            case IMG_FILTER_MEAN_REMOVAL:
                imagefilter($this->_owner->image, IMG_FILTER_MEAN_REMOVAL);
                break;
            case IMG_FILTER_SMOOTH:
                imagefilter($this->_owner->image, IMG_FILTER_SMOOTH, $this->arg1);
                break;
            default:
                return false;
                break;
        }
    }
}

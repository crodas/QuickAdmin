<?php
/*
  +---------------------------------------------------------------------------------+
  | Copyright (c) 2014 César D. Rodas                                               |
  +---------------------------------------------------------------------------------+
  | Redistribution and use in source and binary forms, with or without              |
  | modification, are permitted provided that the following conditions are met:     |
  | 1. Redistributions of source code must retain the above copyright               |
  |    notice, this list of conditions and the following disclaimer.                |
  |                                                                                 |
  | 2. Redistributions in binary form must reproduce the above copyright            |
  |    notice, this list of conditions and the following disclaimer in the          |
  |    documentation and/or other materials provided with the distribution.         |
  |                                                                                 |
  | 3. All advertising materials mentioning features or use of this software        |
  |    must display the following acknowledgement:                                  |
  |    This product includes software developed by César D. Rodas.                  |
  |                                                                                 |
  | 4. Neither the name of the César D. Rodas nor the                               |
  |    names of its contributors may be used to endorse or promote products         |
  |    derived from this software without specific prior written permission.        |
  |                                                                                 |
  | THIS SOFTWARE IS PROVIDED BY CÉSAR D. RODAS ''AS IS'' AND ANY                   |
  | EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED       |
  | WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE          |
  | DISCLAIMED. IN NO EVENT SHALL CÉSAR D. RODAS BE LIABLE FOR ANY                  |
  | DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES      |
  | (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;    |
  | LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND     |
  | ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT      |
  | (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS   |
  | SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE                     |
  +---------------------------------------------------------------------------------+
  | Authors: César Rodas <crodas@php.net>                                           |
  +---------------------------------------------------------------------------------+
*/
namespace crodas\QuickAdmin\Input;

abstract class TBase
{
    protected $instance;
    protected $input;
    protected $annotation;
    protected $col;
    protected $prefix;

    public function __construct($instance, $col, $input, $ann, $prefix = null) 
    {
        $this->instance = $instance;
        $this->col      = $col;
        $this->input    = $input;
        $this->ann      = $ann;
        $this->prefix   = $prefix ?: $this->col['collection'];
    }

    public function isRequired()
    {
        return $this->ann->has('Required');
    }

    public static function label($text)
    {
        $label = "";
        foreach (explode("_", $text) as $n) {
            $label .= ucfirst($n) . " ";
        }
        return $label;
    }

    public function getLabel()
    {
        return self::label($this->input['property']);
        $label = "";
        foreach (explode("_", $this->input['property']) as $n) {
            $label .= ucfirst($n) . " ";
        }
        
        return trim($label);
    }

    public function getName()
    {
        return $this->prefix . '[' . $this->input['property'] . ']';
    }

    public function getId()
    {
        return substr('t' . sha1($this->getName()), 0, 9);
    }

    public function getArgs()
    {
        return ['id' => $this->getId(), 'class' => 'form-control'];
    }

    abstract public function getHtml($form);

}

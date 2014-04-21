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
namespace crodas\QuickAdmin;

use ActiveMongo2\Reflection\Collection;
use ActiveMongo2\Connection;
use crodas\Form\Form;

class QuickAdmin
{
    protected $collection;
    protected $conn;

    public function __construct(Connection $conn, $name)
    {
        $this->collection = $conn->getReflection($name);
        $this->conn       = $conn;
    }

    public function label($name)
    {
        $label = "";
        foreach (explode("_", $name) as $n) {
            $label .= ucfirst($n) . " ";
        }
        
        return trim($label);
    }

    protected function parseAnnotation($prop, &$input)
    {
        foreach ($prop['annotation'] as $ann) {
            switch ($ann['method']) {
            case 'Required':
                $input['required'] = true;
                break;
            case 'Email':
                $input['type'] = 'Email';
                break;
            case 'Longtext':
                $input['type'] = 'Longtext';
                break;
            }
        }
    }

    protected function generateInput($form, &$input)
    {
        $input['id'] = 't'. md5($input['name']);
        switch ($input['type']) {
        case 'String':
        case 'Number':
        case 'Int':
        case 'Float':
            $input['html'] = $form->text($input['name'], ['id' => $input['id']]);
            break;
        case 'Email':
            $input['html'] = $form->text($input['name'], ['type' => 'email', 'id' => $input['id']]);
            break;
        case 'Longtext':
            $input['html'] = $form->textarea($input['name'], ['id' => $input['id']]);
            break;
        }
    }

    public function getFormInputs($form)
    {
        $inputs = array();
        foreach ($this->collection['properties'] as $prop) {
            $input = array(
                'name' => $prop['property'],
                'label' => $this->label($prop['property']),
                'required' => false,
                'type'     => $prop['type'],
            );

            $this->parseAnnotation($prop, $input);
            $this->generateInput($form, $input);

            $inputs[] = $input;
        }

        return $inputs;
    }

    protected function populateDoc($document, $post)
    {
        foreach ($this->collection['properties'] as $property) {
            $name = $property['property'];
            if (array_key_exists($name, $post)) {
                $property->set($document, $post[$name]);
            }
        }
    }

    protected function attemptUpdate($document, $post, &$error)
    {
        $this->populateDoc($document, $post);
        try {
            $this->conn->save($document);
            return true;
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        return false;
    }


    protected function attemptCreate($post, &$error)
    {
        $class = $this->collection['class'];
        $document = new $class;
        $this->populateDoc($document, $post);
        try {
            $this->conn->save($document);
            return true;
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        return false;
    }

    public function handleCreate($post = null, $action = null)
    {
        if (($post = $post ?: $_POST) && !empty($post)) {
            if ($this->attemptCreate($post, $error)) {
                return true;
            }
        }
        $action = $action ?: $_SERVER['REQUEST_URI'];
        $form   = new Form;
        $form->populate($post);
        $inputs = $this->getFormInputs($form);
        $create = _('Create');

        return Templates::get('view/form')
            ->render(compact('action', 'form', 'inputs', 'create', 'error'), true);
    }

    protected function values($object)
    {
        $values = array();
        foreach ($this->collection['properties'] as $property) {
            $name = $property['property'];
            $values[$name] = $property->get($object);
        }

        return $values;
    }

    public function handleUpdate($object, $post = null, $action = null)
    {
        if (($post = $post ?: $_POST) && !empty($post)) {
            if ($this->attemptUpdate($object, $post, $error)) {
                return true;
            }
        }
        $action = $action ?: $_SERVER['REQUEST_URI'];
        $form   = new Form;
        $form->populate(array_merge($this->values($object), $post));
        $inputs = $this->getFormInputs($form);
        $create = _('Update');

        return Templates::get('view/form')
            ->render(compact('action', 'form', 'inputs', 'create', 'error'), true);
    }

}

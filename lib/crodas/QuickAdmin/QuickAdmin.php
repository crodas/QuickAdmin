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
    protected $col;
    protected $rows = 20;
    protected $theme;

    public function __construct(Connection $conn, $name, Theme $theme = null)
    {
        $this->collection = $conn->getReflection($name);
        $this->col        = $conn->$name;
        $this->conn       = $conn;
        $this->theme      = empty($theme) ? new Theme : $theme;
    }

    public function getCollection()
    {
        return $this->col;
    }

    public function label($property)
    {
        $label = "";
        foreach (explode("_", $property['property']) as $n) {
            $label .= ucfirst($n) . " ";
        }
        
        return trim($label);
    }

    protected function parseAnnotation($prop, &$input)
    {
        foreach ($prop['annotation'] as $ann) {
            switch ($ann['method']) {
            case 'Password':
                $input['type'] = 'Password';
                break;
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
        if ($p = $this->isEmbed($prop)) {
            $input['type'] = 'Embed';
            $input['reference'] = $p;
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
        case 'Password':
            $input['html'] = $form->password($input['name'], ['id' => $input['id']]);
            break;
        case 'Email':
            $input['html'] = $form->text($input['name'], ['type' => 'email', 'id' => $input['id']]);
            break;
        case 'Longtext':
            $input['html'] = $form->textarea($input['name'], ['id' => $input['id']]);
            break;
        case 'Embed':
            $inputs = $input['reference']->getFormInputs($form, $input['name']);
            $input['html'] = Templates::get('view/inputs')
                ->render(compact('inputs'), true);
            break;
        }
    }

    public function getFormInputs($form, $name = null)
    {
        $inputs = array();
        $name   = $name ?: $this->collection['name'];
        foreach ($this->collection['properties'] as $prop) {
            $input = array(
                'name' => $name . '[' . $prop['property'] . ']',
                'label' => $this->label($prop),
                'required' => false,
                'type'     => $prop['type'],
            );

            $this->parseAnnotation($prop, $input);
            $this->generateInput($form, $input);

            if (!empty($input['html'])) {
                $inputs[] = $input;
            }
        }

        return $inputs;
    }

    protected function isEmbed($property)
    {
        foreach ($property['annotation'] as $ann) {
            switch ($ann['method']) {
            case 'Embed':
            case 'EmbedOne':
                return new self($this->conn, current($ann['args']));
            }
        }
    }

    protected function populateDoc($document, $post, $update = false)
    {
        $name = $this->collection['collection'];
        if (empty($post[$name]) || !is_array($post[$name])) {
            return false;
        }
        foreach ($this->collection['properties'] as $property) {
            $prop = $property['property'];
            if (array_key_exists($prop, $post[$name])) {
                $value = $post[$name][$prop];
                if (empty($value) && $update && $property['type'] == 'Password') {
                    continue;
                }
                if ($p = $this->isEmbed($property)) {
                    $value = $p->newObject();
                    $p->populateDoc($value, [$p->collection['collection'] => $post[$name][$prop]]);
                }
                $property->set($document, $value);
            }
        }
    }

    protected function attemptToUpdate($document, $post, &$error)
    {
        $this->populateDoc($document, $post, true);
        try {
            $this->conn->save($document);
            return true;
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        return false;
    }

    protected function newObject()
    {
        $class = $this->collection['class'];
        return new $class;
    }

    protected function attemptToCreate($document, $post, &$error)
    {
        $document = $this->newObject(); /* create an empty document */
        return $this->attemptToUpdate($document, $post, $error);
    }

    public function getListColumns()
    {
        $cols = array();
        foreach ($this->collection->properties('@List') as $prop) {
            $cols[$prop['property']] = $this->label($prop);
        }
        return $cols;
    }

    protected function getColRows($cursor)
    {
        $cols = $this->getListColumns();
        $rows = array();
        foreach ($cursor as $row) {
            $array = array();
            $array['__id'] = $this->collection->property('_id')->get($row);
            foreach (array_keys($cols) as $key) {
                $array[$key] = $this->collection->property($key)->get($row);
                if ($array[$key] instanceof \MongoDate) {
                    $array[$key] = date("r", $array[$key]->sec);
                }
            }
            $rows[] = $array;
        }

        return array($cols, $rows);
    }

    public function handleList($url = null, Array $links = array())
    {
        $cursor = $this->col->find()->limit($this->rows);
        $total  = $cursor->count();
        $page   = max(!empty($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1, 1);
        $pages  = range(1, ceil($total / $this->rows));

        $cursor->skip(($page-1) * $this->rows);

        list($cols, $rows) = $this->getColRows($cursor);

        $url  = preg_replace("/.page=\d+/", "", $url ?: $_SERVER['REQUEST_URI']);
        $url .= strpos($url, '?') ? '&' : '?';

        return $this->theme
            ->listView(compact('rows', 'cols', 'page', 'pages', 'url', 'links'));
    }

    protected function prepareForm($action, $data, Array $extra)
    {
        $action = $action ?: $_SERVER['REQUEST_URI'];
        $form   = new Form;
        $form->populate($data);
        $inputs = $this->getFormInputs($form);

        return array_merge($extra, compact('action', 'form', 'inputs'));
    }

    protected function values($post, $object)
    {
        $values = (array)$post;
        $name   = $this->collection['collection'];
        if (!empty($values[$name])) {
            $values[$name] = array();
        }
        foreach ($this->collection['properties'] as $property) {
            $prop = $property['property'];
            $values[$name][$prop] = $property->get($object);
        }

        return $values;
    }

    protected function attemptTo($to, $object, &$post, &$error)
    {
        $method = "attemptTo$to";
        $post   = $post ?: $_POST;

        if  (!empty($post) &&$this->$method($object, $post, $error)) {
            return true;
        }

        return false;
    }

    public function handleCreate($post = null, $action = null)
    {
        $args = $this->genericHandler('Create', null, $post, $action);
        if ($args === true) {
            return $args;
        }

        $args['create'] = _('Create');

        return $this->theme
            ->createView($args);
    }

    protected function genericHandler($type, $object, $post, $action)
    {
        if ($this->attemptTo($type, $object, $post, $error)) {
            return true;
        }

        return $this->prepareForm(
            $action, 
            $type == 'Update' ? $this->values($post, $object) : $post, 
            compact('create', 'error')
        );

    }

    public function handleUpdate($object, $post = null, $action = null)
    {
        $args = $this->genericHandler('Update', $object, $post, $action);
        if ($args === true) {
            return $args;
        }

        $args['create'] = _('Update');

        return $this->theme
            ->updateView($args);
    }

}

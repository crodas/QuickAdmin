<?php
/**
 *  This file was generated with crodas/SimpleView (https://github.com/crodas/SimpleView)
 *  Do not edit this file.
 *
 */

namespace {

    class base_template_d3c3a9097050dfad40060f5ad4ffb2f760696e57
    {
        protected $parent;
        protected $child;
        protected $context;

        public function yield_parent($name, $args)
        {
            $method = "section_" . sha1($name);

            if (is_callable(array($this->parent, $method))) {
                $this->parent->$method(array_merge($this->context, $args));
                return true;
            }

            if ($this->parent) {
                return $this->parent->yield_parent($name, $args);
            }

            return false;
        }

        public function do_yield($name, Array $args = array())
        {
            if ($this->child) {
                // We have a children template, we are their base
                // so let's see if they have implemented by any change
                // this section
                if ($this->child->do_yield($name, $args)) {
                    // yes!
                    return true;
                }
            }

            // Do I have this section defined?
            $method = "section_" . sha1($name);
            if (is_callable(array($this, $method))) {
                // Yes!
                $this->$method(array_merge($this->context, $args));
                return true;
            }

            // No :-(
            return false;
        }

    }

    /** 
     *  Template class generated from View/List.tpl
     */
    class class_3d5f115162eda1ad823035f712dd5e36969f0a75 extends base_template_d3c3a9097050dfad40060f5ad4ffb2f760696e57
    {

        public function render(Array $vars = array(), $return = false)
        {
            $this->context = $vars;

            extract($vars);
            if ($return) {
                ob_start();
            }
            echo "<div class=\"table-responsive\">\n    <table class=\"table table-striped\">\n    <thead>\n        <tr>\n";
            foreach($cols as $col) {
                $this->context['col'] = $col;
                echo "        <th>";
                echo htmlentities($col, ENT_QUOTES, 'UTF-8', false);
                echo "</th>\n";
            }
            if (!empty($links)) {
                echo "        <th></th>\n";
            }
            echo "        </tr>\n    </thead>\n    <tbody>\n";
            foreach($rows as $row) {
                $this->context['row'] = $row;
                echo "          <tr>\n";
                foreach($row as $key => $data) {
                    $this->context['key'] = $key;
                    $this->context['data'] = $data;
                    if ($key !== '__id') {
                        echo "                    <td>";
                        echo htmlentities($data, ENT_QUOTES, 'UTF-8', false);
                        echo "</td>\n";
                    }
                }
                if (!empty($links)) {
                    foreach($links as $text => $link) {
                        $this->context['text'] = $text;
                        $this->context['link'] = $link;
                        echo "                    <td><a href=\"" . (str_replace('{id}', $row['__id'], $link)) . "\" class=\"btn btn-success\">" . ($text) . "</a></td>\n";
                    }
                }
                echo "          </tr>\n";
            }
            echo "    </tbody>\n    </table>\n\n    <ul class=\"pagination\">\n        <li>\n";
            if ($page > 1) {
                echo "            <a href=\"" . ($url) . "page=1\">&laquo;</a>\n";
            }
            else {
                echo "            <a>&laquo;</a>\n";
            }
            echo "        </li>\n";
            foreach($pages as $p) {
                $this->context['p'] = $p;
                if ($p == $page) {
                    echo "                <li><a>" . ($p) . "</a></li>\n";
                }
                else {
                    echo "                <li><a href=\"" . ($url) . "page=" . ($p) . "\">" . ($p) . "</a></li>\n";
                }
            }
            echo "        <li>\n";
            if ($tpages != $page) {
                echo "            <a href=\"" . ($url) . "page=" . ($tpages) . "\">&raquo;</a>\n";
            }
            else {
                echo "            <a>&raquo;</a>\n";
            }
            echo "        </li>\n    </ul>\n</div>\n\n";

            if ($return) {
                return ob_get_clean();
            }

        }
    }

    /** 
     *  Template class generated from View/Inputs.tpl
     */
    class class_0a3f63e96b2715efa9f87b9d30fa220b60a135e0 extends base_template_d3c3a9097050dfad40060f5ad4ffb2f760696e57
    {

        public function render(Array $vars = array(), $return = false)
        {
            $this->context = $vars;

            extract($vars);
            if ($return) {
                ob_start();
            }
            foreach($inputs as $input) {
                $this->context['input'] = $input;
                if (empty($input->shown)) {
                    echo "    <div class=\"form-group\">\n        <label for=\"";
                    echo htmlentities($input->getId(), ENT_QUOTES, 'UTF-8', false);
                    echo "\" class=\"col-sm-2 control-label\">\n            ";
                    echo htmlentities($input->getLabel(), ENT_QUOTES, 'UTF-8', false);
                    echo "\n";
                    if ($input->isRequired()) {
                        echo "                (*)\n";
                    }
                    echo "        </label>\n        <div class=\"col-sm-10\">\n            " . ($input->getHtml($form)) . "\n        </div>\n    </div>\n";
                    $input->shown = true;
                }
            }

            if ($return) {
                return ob_get_clean();
            }

        }
    }

    /** 
     *  Template class generated from View/Form.tpl
     */
    class class_ac2b2c27c15f9c306f6b819780a4774817c2509c extends base_template_d3c3a9097050dfad40060f5ad4ffb2f760696e57
    {

        public function render(Array $vars = array(), $return = false)
        {
            $this->context = $vars;

            extract($vars);
            if ($return) {
                ob_start();
            }
            echo $form->open($action, 'POST', ['class' => 'form-horizontal', 'enctype' => 'multipart/form-data']) . "\n\n";
            if (!empty($error)) {
                echo "    <div class=\"alert alert-danger\">";
                echo htmlentities($error, ENT_QUOTES, 'UTF-8', false);
                echo "</div>\n";
            }
            echo "\n" . ($self->inputsView(compact('form', 'inputs'))) . "\n\n<div class=\"form-group\">\n    <div class=\"col-sm-offset-2 col-sm-10\">\n        <button type=\"submit\" class=\"btn btn-default\">";
            echo htmlentities($create, ENT_QUOTES, 'UTF-8', false);
            echo "</button>\n    </div>\n</div>\n\n</form>\n" . ($form->close()) . "\n";

            if ($return) {
                return ob_get_clean();
            }

        }
    }

}

namespace crodas\QuickAdmin {

    class Templates
    {
        public static function getAll()
        {
            return array (
                0 => 'view/list',
                1 => 'view/inputs',
                2 => 'view/form',
            );
        }

        public static function exec($name, Array $context = array(), Array $global = array())
        {
            $tpl = self::get($name);
            return $tpl->render(array_merge($global, $context));
        }

        public static function get($name, Array $context = array())
        {
            static $classes = array (
                'view/list.tpl' => 'class_3d5f115162eda1ad823035f712dd5e36969f0a75',
                'view/list' => 'class_3d5f115162eda1ad823035f712dd5e36969f0a75',
                'view/inputs.tpl' => 'class_0a3f63e96b2715efa9f87b9d30fa220b60a135e0',
                'view/inputs' => 'class_0a3f63e96b2715efa9f87b9d30fa220b60a135e0',
                'view/form.tpl' => 'class_ac2b2c27c15f9c306f6b819780a4774817c2509c',
                'view/form' => 'class_ac2b2c27c15f9c306f6b819780a4774817c2509c',
            );
            $name = strtolower($name);
            if (empty($classes[$name])) {
                throw new \RuntimeException("Cannot find template $name");
            }

            $class = "\\" . $classes[$name];
            return new $class;
        }
    }

}

<?php
namespace PHPDatastream\Template;

interface Renderer
{

    public function render($template, $data = []);
}


<?php

namespace App\Controls;

use Nette\Application\UI\Control;

class Gallery extends Control
{
    public function render($images, $name)
    {
        $images = explode(PHP_EOL, trim($images));
        $images = array_map('trim', $images);
        $template = $this->template;
        $template->setFile(__DIR__ . '/gallery.latte');
        $template->name = $name;
        $template->images = $images;
        $template->render();
    }
}
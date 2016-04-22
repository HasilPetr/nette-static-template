<?php

namespace App\Presenters;

use App\Controls\Gallery;
use Nette;

class StaticPresenter extends \Nette\Application\UI\Presenter
{

    function findLayoutTemplateFile()
    {
        return $this->getParameter('layout');
    }

    protected function beforeRender()
    {
        parent::beforeRender();
        $this->template->setFile($this->getParameter('template'));
    }

    public function createComponentContactForm()
    {
        return new \App\Forms\ContactForm();
    }

    public function createComponentGallery()
    {
        return new Gallery();
    }

}

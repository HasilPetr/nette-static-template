<?php

namespace App\Presenters;

use Nette;
use App\Model;

class PagePresenter extends \Nette\Application\UI\Presenter
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
        return new \Third\ContactForm();
    }

}

<?php

namespace Third;

use Nette\Application\UI\Form;
use Nette\Mail\Message;
use Nette\Mail\SendmailMailer;

class ContactForm extends Form
{

    function __construct()
    {
        $this->addText('email')
            ->setValue('@')
            ->setAttribute('style', 'display:none;');
        $this->addText('antispam1')
            ->setType('email')
            ->setRequired()
            ->addRule(Form::EMAIL)
            ->setAttribute('placeholder', 'Email');
        $this->addTextArea('text')
            ->setAttribute('placeholder', 'Message');
        $this->addSubmit('submit', 'Send')
            ->setAttribute('style', 'display:none;');
        $this->addSubmit('antispam2', 'Send');
        $this->addHidden('check')
            ->setValue(time());
        $this->onSuccess[] = array($this, 'processContactForm');
    }

    function processContactForm(Form $form, $values)
    {
        if ($this->getPresenter()->getRequest()->getPost('antispam2') != 'Send' ||
            $values['email'] != '@' ||
            $values['check'] > time() - 5) {
            $this->getPresenter()->flashMessage('Spam detected');
        } else {
            $message = new Message();
            $message->addTo('your@email.eml')
                ->setFrom($values['antispam1'])
                ->setSubject('Message')
                ->setBody($values['text']);
            $mailer = new SendmailMailer();
            $mailer->send($message);
            $this->getPresenter()->flashMessage('Message sent');
        }
        $this->getPresenter()->redirect('this');
    }

}
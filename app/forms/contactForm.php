<?php

namespace App\Forms;

use Nette\Application\UI\Form;
use Nette\Mail\Message;
use Nette\Mail\SendmailMailer;

class ContactForm extends Form
{

    function __construct()
    {
        $this->addText('email', 'Email')
            ->setValue('@');
        $this->addText('antispam1', 'Email')
            ->setType('email')
            ->setRequired()
            ->addRule(Form::EMAIL);
        $this->addTextArea('text', 'Zpráva');
        $this->addSubmit('submit', 'Odeslat');
        $this->addSubmit('antispam2', 'Odeslat');
        $this->addHidden('check')
            ->setValue(time());
        $this->onSuccess[] = array($this, 'processContactForm');
    }

    function processContactForm(Form $form, $values)
    {
        if ($this->getPresenter()->getRequest()->getPost('antispam2') != 'Odeslat' || // kontrola odesílacího tlačítka
            $values['email'] != '@' || // kontrola nezměněné hodnoty
            $values['check'] > time() - 5) { // kontrola minimální prodlevy
            $this->getPresenter()->flashMessage('Spam', 'danger');
        } else {
//            $message = new Message();
//            $message->addTo('your@email.eml')
//                ->setFrom($values['antispam1'])
//                ->setSubject('Message')
//                ->setBody($values['text']);
//            $mailer = new SendmailMailer();
//            $mailer->send($message);
            $this->getPresenter()->flashMessage('Zpráva odeslána');
        }
        $this->getPresenter()->redirect('this');
    }

}
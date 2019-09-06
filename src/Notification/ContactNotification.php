<?php


namespace App\Notification;


use App\Entity\Contact;
use Twig\Environment;

class ContactNotification
{


    // Environment pour pouvoir générer un email au format HTML
    public  function  __construct(\Swift_Mailer $mailer, Environment $renderer)
    {
    }

    public function notify(Contact $contact)
    {

    }


}
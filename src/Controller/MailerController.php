<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Mailjet\Resources;
use Mailjet\Client;


class MailerController extends AbstractController
{

    /**
     * Undocumented function
     *
     * @Route("/email",name="email")
     * @return void
     */
    public function sendmail() {
        $mj = new Client('54abf7bd7c959b059abdee6778722a2c','89a12b1d1e22e0c08167316926b02e1e',true,['version' => 'v3.1']);
        $body = [
          'Messages' => [
            [
              'From' => [
                'Email' => "bonnal.tristan@hotmail.fr",
                'Name' => "Tristan"
              ],
              'To' => [
                [
                  'Email' => "bonnal.tristan@hotmail.fr",
                  'Name' => "Tristan"
                ]
              ],
              'Subject' => "Greetings from Mailjet.",
              'TextPart' => "My first Mailjet email",
              'HTMLPart' => "<h3>Dear passenger 1, welcome to <a href='https://www.mailjet.com/'>Mailjet</a>!</h3><br />May the delivery force be with you!",
              'CustomID' => "AppGettingStartedTest"
            ]
          ]
        ];
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        $response->success() && ($response->getData());

    }

}
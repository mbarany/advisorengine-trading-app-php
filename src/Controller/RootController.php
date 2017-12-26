<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class RootController extends AbstractController
{
    /**
     * @return Response
     */
    public function rootAction(): Response
    {
        return $this->render(
            'root/index.html.twig'
        );
    }
}

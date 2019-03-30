<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class RootController extends AbstractController {

    /**
     * @Route("/", name="app_root")
     */
    public function root() {
        return $this->render("index.html.twig", []);
    }

}

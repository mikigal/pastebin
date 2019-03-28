<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/panel")
 */
class PanelController extends AbstractController {

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/")
     */
    public function root() {
        return $this->render("panel/panel.html.twig", []);
    }

}
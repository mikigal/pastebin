<?php

namespace App\Controller;

use App\Repository\PasteRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/panel")
 */
class PanelController extends AbstractController {

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/", name="app_panel")
     */
    public function root(PasteRepository $pasteRepository) {
        return $this->render("panel/panel.html.twig", [
            'sidebar' => $pasteRepository->getSidebar()
        ]);
    }
}
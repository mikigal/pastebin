<?php

namespace App\Controller;

use App\Repository\PasteRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api")
 */
class RestController extends AbstractController {

    /**
     * @Route("/read")
     * @Method("GET")
     */
    public function read(Request $request, PasteRepository $pasteRepository) {
        $name = $request->get('name');
        if ($name == null) {
            return $this->json([
                'error' => 1,
                'message' => 'Missing name parameter'
            ], 400);
        }

        $paste = $pasteRepository->findOneByName($name);
        if ($paste == null) {
            return $this->json([
               'error' => 2,
               'message' => 'Paste does not exists'
            ]);
        }

        return $this->json($paste);
    }

    /**
     * @Route("/last")
     * @Method("GET")
     */
    public function last(PasteRepository $pasteRepository) {
        return $this->json($pasteRepository->getSidebar());
    }
}
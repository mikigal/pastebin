<?php

namespace App\Controller;

use App\Repository\PasteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/pastes", name="app_pastes")
     */
    public function pastes(Request $request, PasteRepository $pasteRepository) {
        $user = $this->get('security.token_storage')->getToken()->getUser();

        return $this->render('panel/pastes.html.twig', [
            'sidebar' => $pasteRepository->getSidebar(),
            'pastes' => $pasteRepository->findUserPastes($user),
            'removed' => $request->get('removed')
        ]);
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/delete/{name}", name="app_delete")
     */
    public function delete(string $name, PasteRepository $pasteRepository, EntityManagerInterface $entityManager) {
        $paste = $pasteRepository->findOneByName($name);
        $user = $this->get('security.token_storage')->getToken()->getUser();

        if ($paste == null) {
            return  $this->redirectToRoute('app_pastes', [
                'removed' => false
            ]);
        }

        if ($paste->getOwner() != $user->getId()) {
            return  $this->redirectToRoute('app_pastes', [
                'removed' => false,
            ]);
        }

        $entityManager->remove($paste);
        $entityManager->flush();
        return  $this->redirectToRoute('app_pastes', [
            'removed' => true
        ]);
    }
}
<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\PasteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

/**
 * @Route("/panel")
 */
class PanelController extends AbstractController {

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/", name="app_panel")
     */
    public function root(Request $request, PasteRepository $pasteRepository) {
        return $this->render("panel/panel.html.twig", [
            'sidebar' => $pasteRepository->getSidebar(),
            'password' => $request->get('password')
        ]);
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/pastes", name="app_pastes")
     */
    public function pastes(Request $request, PasteRepository $pasteRepository) {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        if ($user == 'anon.') {
            return $this->redirectToRoute('app_root');
        }

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
        if ($user == 'anon.') {
            return $this->redirectToRoute('app_root');
        }

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

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/password/{id}", name="app_password")
     */
    public function password(Request $request, int $id, EncoderFactoryInterface $encoderFactory, EntityManagerInterface $entityManager) {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        if ($user == 'anon.' || $user->getId() != $id) {
            return $this->redirectToRoute('app_panel');
        }

        $old = $request->get('old');
        $new = $request->get('new');
        $repeat = $request->get('repeat');

        if ($old == null || $new == null || $repeat == null) {
            return $this->redirectToRoute('app_panel');
        }

        if ($new != $repeat) {
            return $this->redirectToRoute('app_panel', ['password' => 2]);
        }

        if (!$encoderFactory->getEncoder(User::class)->isPasswordValid($user->getPassword(), $old, $user->getSalt())) {
            return $this->redirectToRoute('app_panel', ['password' => 3]);
        }

        if ($old != $new) {
            return $this->redirectToRoute('app_panel', ['password' => 4]);
        }
        $user->setPassword($encoderFactory->getEncoder(User::class)->encodePassword($new, $user->getSalt()));
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('app_panel', ['password' => 1]);
    }
}
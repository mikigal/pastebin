<?php

namespace App\Controller;

use App\Entity\Paste;
use App\Form\PasteType;
use App\Repository\PasteRepository;
use App\Repository\UserRepository;
use App\Service\RecaptchaService;
use App\Utils\Utils;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RootController extends AbstractController {

    /**
     * @Route("/", name="app_root")
     */
    public function root(Request $request, PasteRepository $pasteRepository, EntityManagerInterface $entityManager, RecaptchaService $recaptchaService) {
        $paste = new Paste();
        $form = $this->createForm(PasteType::class, $paste);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$recaptchaService->checkCaptcha($request->get('g-recaptcha-response'))) {
                $form->addError(new FormError('Are you a robot? Make captcha'));
                return $this->render('index.html.twig', [
                    'form' => $form->createView(),
                    'recaptcha' => getenv('RECAPTCHA_SITEKEY'),
                    'sidebar' => $pasteRepository->getSidebar()
                ]);
            }

            if (strlen(preg_replace('/\s/', '', $paste->getContent())) == 0) {
                $form->addError(new FormError('Content cant be empty'));
                return $this->render('index.html.twig', [
                    'form' => $form->createView(),
                    'recaptcha' => getenv('RECAPTCHA_SITEKEY'),
                    'sidebar' => $pasteRepository->getSidebar()
                ]);
            }

            $user = $this->get('security.token_storage')->getToken()->getUser();
            $paste->setOwner($user == 'anon.' ? null : $user->getId());
            $paste->setUploadDate(new DateTime());
            $paste->setContent(base64_encode(str_replace(array("\r\n", "\r", "\n"), "[new-line]", $paste->getContent())));
            $paste->setName(Utils::getRandomString(10));

            $entityManager->persist($paste);
            $entityManager->flush();

            return $this->redirectToRoute('app_paste', [
                'name' => $paste->getName()
            ], 301);
        }

        return $this->render('index.html.twig', [
            'form' => $form->createView(),
            'recaptcha' => getenv('RECAPTCHA_SITEKEY'),
            'sidebar' => $pasteRepository->getSidebar()
        ]);
    }

    /**
     * @Route("/view/{name}", name="app_paste")
     */
    public function paste(string $name, PasteRepository $pasteRepository, UserRepository $userRepository) {
        $paste = $pasteRepository->findOneByName($name);
        if ($paste == null) {
            return $this->render('paste.html.twig', [
                'paste' => null,
                'sidebar' => $pasteRepository->getSidebar()
            ]);
        }

        $user = $this->get('security.token_storage')->getToken()->getUser();
        if ($paste->getVisibility() == 3 && ($user == "anon." || $paste->getOwner() != $user->getId())) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('paste.html.twig', [
            'content' => explode('[new-line]', base64_decode($paste->getContent())),
            'paste' => $paste,
            'username' => $paste->getOwner() != null ? $userRepository->findOneBy(['id' => $paste->getOwner()])->getUsername() : 'Guest',
            'sidebar' => $pasteRepository->getSidebar()
        ]);
    }
}

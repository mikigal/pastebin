<?php

namespace App\Controller;

use App\Entity\Paste;
use App\Form\PasteType;
use App\Service\RecaptchaService;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RootController extends AbstractController {

    /**
     * @Route("/", name="app_root")
     */
    public function root(Request $request, RecaptchaService $recaptchaService) {
        $paste = new Paste();
        $form = $this->createForm(PasteType::class, $paste);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$recaptchaService->checkCaptcha($request->get('g-recaptcha-response'))) {
                $form->addError(new FormError('Are you a robot? Make captcha'));
                return $this->render('index.html.twig', [
                    'form' => $form->createView(),
                    'recaptcha' => getenv('RECAPTCHA_SITEKEY')
                ]);
            }

            $user = $this->get('security.token_storage')->getToken()->getUser();
            $paste->setOwner($user == 'anon.' ? null : $user->getId());
            $paste->setUploadDate(new DateTime());
            $paste->setContent(base64_encode($paste->getContent()));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($paste);
            $entityManager->flush();

            return $this->render('index.html.twig', [ //TODO: Redirect to preview
                'form' => $form->createView(),
                'recaptcha' => getenv('RECAPTCHA_SITEKEY')
            ]);
        }

        return $this->render('index.html.twig', [
            'form' => $form->createView(),
            'recaptcha' => getenv('RECAPTCHA_SITEKEY')
        ]);
    }

}

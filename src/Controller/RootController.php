<?php

namespace App\Controller;

use App\Entity\Paste;
use App\Form\PasteType;
use App\Service\RecaptchaService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RootController extends AbstractController {

    /**
     * @Route("/", name="app_root")
     */
    public function root(Request $request, LoggerInterface $logger, RecaptchaService $recaptchaService) {
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

            $logger->alert("Submitted!");
        }

        return $this->render('index.html.twig', [
            'form' => $form->createView(),
            'recaptcha' => getenv('RECAPTCHA_SITEKEY')
        ]);
    }

}

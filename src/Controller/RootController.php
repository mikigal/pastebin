<?php

namespace App\Controller;

use App\Entity\Paste;
use App\Form\PasteType;
use App\Service\RecaptchaService;
use App\Utils\Utils;
use DateTime;
use Psr\Log\LoggerInterface;
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

            if (strlen(preg_replace('/\s/', '', $paste->getContent())) == 0) {
                $form->addError(new FormError('Content cant be empty'));
                return $this->render('index.html.twig', [
                    'form' => $form->createView(),
                    'recaptcha' => getenv('RECAPTCHA_SITEKEY')
                ]);
            }

            $user = $this->get('security.token_storage')->getToken()->getUser();
            $paste->setOwner($user == 'anon.' ? null : $user->getId());
            $paste->setUploadDate(new DateTime());
            $paste->setContent(base64_encode($paste->getContent()));
            $paste->setName(Utils::getRandomString(10));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($paste);
            $entityManager->flush();

            return $this->redirectToRoute('app_paste', [
                'name' => $paste->getName()
            ], 301);
        }

        return $this->render('index.html.twig', [
            'form' => $form->createView(),
            'recaptcha' => getenv('RECAPTCHA_SITEKEY')
        ]);
    }


    /**
     * @Route("/{name}", name="app_paste")
     */
    public function paste(string $name) {
        $paste = $this->getDoctrine()->getRepository(Paste::class)->findOneByName($name);
        if ($paste == null) {
            return $this->render('paste.html.twig', [
                'not_found' => true
            ]);
        }

        return $this->render('paste.html.twig', [
            'not_found' => false,
            'content' => base64_decode($paste->getContent())
        ]);
    }
}

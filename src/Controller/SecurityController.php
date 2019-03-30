<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use App\Service\RecaptchaService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController {

    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response {
        if ($this->getUser() != null) {
            return $this->render("index.html.twig");
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, EncoderFactoryInterface $encoderFactory, RecaptchaService $recaptchaService) {
        if ($this->getUser() != null) {
            return $this->render("index.html.twig");
        }

        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$recaptchaService->checkCaptcha($request->get("g-recaptcha-response"))) {
                $form->addError(new FormError("Are you a robot? Make captcha"));
                return $this->render("security/register.html.twig", [
                    "form" => $form->createView(),
                    "recaptcha" => getenv("RECAPTCHA_SITEKEY")
                ]);
            }

            $user->setSalt(md5(uniqid()));
            $user->setPassword($encoderFactory->getEncoder(User::class)->encodePassword($form->get("password")->getData(), $user->getSalt()));
            $user->setRegisterDate(new \DateTime());

            $entityManager = $this->getDoctrine()->getManager();

            if ($entityManager->getRepository(User::class)->count(['username' => $user->getUsername()]) != 0) {
                $form->addError(new FormError("User with this username already exists"));
                return $this->render("security/register.html.twig", [
                    "form" => $form->createView(),
                    "recaptcha" => getenv("RECAPTCHA_SITEKEY")
                ]);
            }

            if ($entityManager->getRepository(User::class)->count(['mail' => $user->getMail()]) != 0) {
                $form->addError(new FormError("User with this mail already exists"));
                return $this->render("security/register.html.twig", [
                    "form" => $form->createView(),
                    "recaptcha" => getenv("RECAPTCHA_SITEKEY")
                ]);
            }

            $entityManager->persist($user);
            $entityManager->flush();

            $token = new UsernamePasswordToken($user, null, "main", $user->getRoles());
            $this->container->get("security.token_storage")->setToken($token);
            $this->container->get("session")->set("_security_main", serialize($token));

            return $this->redirect("/panel");
        }
        return $this->render("security/register.html.twig", [
            "form" => $form->createView(),
            "recaptcha" => getenv("RECAPTCHA_SITEKEY")
        ]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout() {

    }
}

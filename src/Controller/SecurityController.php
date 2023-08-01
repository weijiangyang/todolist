<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


class SecurityController extends AbstractController
{
    #[Route('/connextion', name: 'security_login', methods:['GET','POST'])]
    public function index(AuthenticationUtils $utils): Response
    {
        return $this->render('pages/security/login.html.twig', [
            'last_username' => $utils->getLastUsername(),
            'error' => $utils->getLastAuthenticationError()
        ]);
    }
    /**
     * This function permet the current user to deconnect from the site 
     *
     * @return Response
     */
    #[Route('/deconnexion', name: 'security_logout', methods: ['GET', 'POST'])]
    public function logout():Response
    {
        // nothing to do 
    }

    #[Route('/inscription', name: 'security_registration', methods: ['GET', 'POST'])]
    public function registration( Request $request, EntityManagerInterface $em): Response
    {
        $user = new User;
        $user->setRoles(['ROLE_USER']);
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $user = $form->getData();
            $em->persist($user);
            $em->flush();
           
            
            return $this->redirectToRoute('security_login');
        }
        return $this->render('pages/security/registration.html.twig', [
            'form' => $form->createView()
        ]);
    }
}



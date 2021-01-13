<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrierungController extends AbstractController
{
    /**
     * @Route("/reg", name="reg")
     */
    public function reg(Request $request, UserPasswordEncoderInterface $passEncoder)
    {
        $regForm = $this->createFormBuilder()
        ->add('username', TextType::class,[
            'label' => 'Mitarbeiter'])
        ->add('password', RepeatedType::class,[
            'type' => PasswordType::class,
            'required' => true,
            'first_options' => ['label' => 'Passwort'],
            'second_options' => ['label' => 'Passwort wiederholen']
        ])

        ->add('registrieren', SubmitType::class)
        ->getForm()
        ;

        $regForm->handleRequest($request);

        if($regForm->isSubmitted()) {
            $eingabe = $regForm->getData();
            $user = new User();
            $user->setUsername($eingabe['username']);

            $user->setPassword(
                $passEncoder->encodePassword($user, $eingabe['password'])
            );

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirect($this->generateUrl('home'));
        }

        return $this->render('registrierung/index.html.twig', [
            'regform' => $regForm->createView()
        ]);
    }
}

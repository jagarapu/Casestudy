<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\ChangePasswordType;
use App\Form\Type\UserType;
use App\Service\UserManager;
use App\Service\MailManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Component\Security\Core\Security;

/**
 * Class UserController
 * @package App\Controller
 */
class UserController extends AbstractController
{
    /**
     * @Route("/home", name="home_page")
     */
    public function home(Request $request)
    {
        return $this->render('user/home_page.html.twig');
    }

    /**
     * Lists all user entities.
     *
     * @Route("/user/list", name="user_index")
     */
    public function index(UserManager $userManager)
    {
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository(User::class)->findAll();

        return $this->render('user/list.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * Creates a new user entity.
     *
     * @Route("/user/new", name="user_new")
     */
    public function new(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $password = $user->generatePassword();
            $defaultEncoder = new MessageDigestPasswordEncoder('sha512', true, 5000);
            $encoders = [
                User::class => $defaultEncoder,
            ];
            $encoderFactory = new EncoderFactory($encoders);
            $encoder = $encoderFactory->getEncoder($user);
            $user->encodePassword($encoder);
            $user->setRawPassword($password);
            $user->setIsEnabled(true);

            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('home_page');
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing user entity.
     *
     * @Route("/user/{id}/edit", name="user_edit")
     */
    public function edit(Request $request, User $user, UserManager $userManager)
    {
        $editForm = $this->createForm(UserType::class, $user);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'edit_form' => $editForm->createView()
        ]);
    }

    /**
     * Activates/Deactiavtes a user entity.
     *
     * @Route("/{id}/toggle/activation", name="user_toggle_activation")
     */
    public function toggleActivation(Request $request, User $user)
    {
        $em = $this->getDoctrine()->getManager();
        $user->setIsEnabled(!($user->getIsEnabled()));
        $em->flush();
        if($user->getIsEnabled()){
            $message = $user->getUsername() . ' user activated successfully';
        } else {
            $message = $user->getUsername() . ' user deactivated successfully';
        }

        return $this->redirectToRoute('user_index');
    }

    /**
     * Generates new password
     *
     * @Route("/forgot-password", name="forgot_password", options = {"expose"=true})
     */
    public function forgotPassword(Request $request, MailManager $mailManager, EncoderFactoryInterface $encoderFactory)
    {
        $em = $this->getDoctrine()->getManager();
        $email = $request->query->get('email');
        $user = $em->getRepository(User::class)->findOneByEmail($email);
        if($user){
            $password = $user->generatePassword();
            $encoder = $encoderFactory->getEncoder($user);
            $user->encodePassword($encoder);
            $user->setRawPassword($password);
            $em->persist($user);
            $em->flush();
            $mailManager->forgotPassword($user);
            $this->get('session')->getFlashBag()->set(
                'flashSuccess',
                'A new password has been sent to ' . $email
            );
        } else {
            $this->get('session')->getFlashBag()->set(
                'flashError',
                'This email is not registered with SpadeCRM'
            );
        }

        return $this->redirectToRoute('app_login');
    }

    /**
     * Change password of a user
     * @Route("/my-profile/{id}/changepassword", name="change_password")
     */
    public function changePassword(Request $request, User $user, UserManager $userManager, MailManager $mailManager)
    {
        $form = $this->createForm(ChangePasswordType::class, $user);
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $userManager->manageUpdatedPassword($user, false);
//                $mailManager->changePasswordEmail($user);
                $this->get('session')->getFlashBag()->add('flashSuccess', 'Password Changed Successfully!');
                return $this->redirectToRoute('office_list');
            }
        }

        return $this->render(
            'user/password_change.html.twig',
            [
                'form' => $form->createView(),
                'user' => $user,
            ]
        );
    }

    /**
     * @Route("/my-profile", name="my_profile")
     */
    public function myProfile(Request $request)
    {
        // logged in user
        $user = $this->getUser();

        return $this->render(
            'user/my_profile.html.twig',
            [
                'user' => $user,
            ]
        );
    }

}
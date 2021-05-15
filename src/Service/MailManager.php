<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use Symfony\Component\Templating\EngineInterface;
use Twig\Environment;

class MailManager
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var \Swift_Mailer
     */
    private $mailer;
    /**
     * @var Environment
     */
    private $twig;
    /**
     * @var string
     */
    private $mailerUser;

    /**
     * MailManager constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param \Swift_Mailer $mailer
     * @param Environment $twig
     * @param string $mailerUser
     */
    public function __construct(EntityManagerInterface $entityManager, \Swift_Mailer $mailer,
                                Environment $twig, $mailerUser
    ) {
        $this->entityManager = $entityManager;
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->mailerUser = $mailerUser;
    }

    /**
     * @param User $user
     * @return null
     */
    public function registration(User $user)
    {
        $emailTemplate = $this->twig->render(
            'user/email/registration.html.twig',
            [
                'password' => $user->getRawPassword(),
                'username' => $user->getUsername(),
                'userFirstName' => $user->getFirstName(),
            ]
        );

        $message = (new \Swift_Message('TraceRecruit Registration'))
            ->setFrom($this->mailerUser)
            ->setTo($user->getEmail())
            ->setBody($emailTemplate,'text/html')
        ;

        $this->mailer->send($message);
    }

    /**
     * @param User $user
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function forgotPassword(User $user)
    {
        $emailTemplate = $this->twig->render(
            'user/email/forgotPassword.html.twig',
            [
                'userFirstName' => $user->getFirstName(),
                'password' => $user->getRawPassword(),
                'username' => $user->getUsername(),
            ]
        );

        $message = (new \Swift_Message('TraceRecruit Forgot Password'))
            ->setFrom($this->mailerUser)
            ->setTo($user->getEmail())
            ->setBody($emailTemplate,'text/html')
        ;

        $this->mailer->send($message);
    }

    /**
     * @param User $user
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function changePasswordEmail(User $user)
    {
        $emailTemplate = $this->twig->render(
            'user/email/change_password.html.twig',
            [
                'password' => $user->getRawPassword(),
                'username' => $user->getUsername(),
                'userFirstName' => $user->getFirstName(),
            ]
        );

        $message = (new \Swift_Message('TraceRecruit Change Password'))
            ->setFrom($this->mailerUser)
            ->setTo($user->getEmail())
            ->setBody($emailTemplate,'text/html')
        ;

        $this->mailer->send($message);
    }

}
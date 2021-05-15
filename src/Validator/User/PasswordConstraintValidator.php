<?php

namespace App\Validator\User;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class PasswordConstraintValidator extends ConstraintValidator
{
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var  EncoderFactory
     */
    private $encoderFactory;

    /**
     * PasswordConstraintValidator constructor.
     *
     * @param EntityManagerInterface $em
     * @param EncoderFactoryInterface $encoderFactory
     */
    public function __construct(EntityManagerInterface $em, EncoderFactoryInterface $encoderFactory)
    {
        $this->em = $em;
        $this->encoderFactory = $encoderFactory;
    }

    /**
     * @param User $user
     */
    public function validate($user, Constraint $constraint)
    {
        $rawPassword = $user->getRawPassword();
        if (null === $rawPassword || '' === $rawPassword) {
            return;
        }
        if ($constraint->minLength > 0 && (mb_strlen($rawPassword) < $constraint->minLength)) {
            $this->context->addViolation($constraint->tooShortMessage, ['{{length}}' => $constraint->minLength]);
        }
        if ($constraint->requireLetters && !preg_match('/\pL/u', $rawPassword)) {
            $this->context->addViolation($constraint->missingLettersMessage);
        }
        if ($constraint->requireCaseDiff && !preg_match('/(\p{Ll}+.*\p{Lu})|(\p{Lu}+.*\p{Ll})/u', $rawPassword)) {
            $this->context->addViolation($constraint->requireCaseDiffMessage);
        }
        if ($constraint->requireNumbers && !preg_match('/\pN/u', $rawPassword)) {
            $this->context->addViolation($constraint->missingNumbersMessage);
        }
        if ($constraint->requireSpecialCharacter && !preg_match('/[^p{Ll}\p{Lu}\pL\pN]/u', $rawPassword)) {
            $this->context->addViolation($constraint->missingSpecialCharacterMessage);
        }

    }

}
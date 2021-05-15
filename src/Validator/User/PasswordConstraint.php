<?php

namespace App\Validator\User;

use App\Entity\User;
use Symfony\Component\Validator\Constraint;

class PasswordConstraint extends Constraint
{
    public $tooShortMessage = 'Your password must be at least {{length}} characters long.';
    public $missingLettersMessage = 'Your password must include at least one letter.';
    public $requireCaseDiffMessage = 'Your password must include both upper and lower case letters.';
    public $missingNumbersMessage = 'Your password must include at least one number.';
    public $missingSpecialCharacterMessage = 'Your password must contain at least one special character.';
    public $previouslyUsedMessage = 'You may not re-use old passwords used within 90 days.';

    public $minLength = User::PASSWORD_LENGTH;
    public $requireLetters = true;
    public $requireCaseDiff = true;
    public $requireNumbers = true;
    public $requireSpecialCharacter = true;

    public function validatedBy()
    {
        return 'password_constraint';
    }

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
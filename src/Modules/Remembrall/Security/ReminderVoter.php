<?php


namespace App\Modules\Remembrall\Security;


use App\Modules\Remembrall\Entity\Reminder;
use App\Modules\Security\Entity\User;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ReminderVoter extends Voter
{
    public const VIEW = 'view';
    public const EDIT = 'edit';
    public const DELETE = 'delete';

    protected function supports(string $attribute, $subject): bool
    {
        if (!in_array($attribute, [self::VIEW, self::EDIT, self::DELETE])) {
            return false;
        }

        if (!$subject instanceof Reminder) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }
        /** @var Reminder $reminder */
        $reminder = $subject;

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($reminder, $user);
            case self::EDIT:
                return $this->canEdit($reminder, $user);
            case self::DELETE:
                return $this->canDelete($reminder, $user);
        }
        throw new LogicException('This code should not be reached');
    }

    private function canView(Reminder $reminder, User $user): bool
    {
        return $this->isOwner($reminder, $user);
    }

    private function isOwner(Reminder $reminder, User $user): bool
    {
        return $reminder->getOwner() === $user;
    }

    private function canEdit(Reminder $reminder, User $user): bool
    {
        return $this->isOwner($reminder, $user);
    }

    private function canDelete(Reminder $reminder, User $user): bool
    {
        return $this->isOwner($reminder, $user);
    }
}
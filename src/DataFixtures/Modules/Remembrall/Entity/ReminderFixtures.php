<?php


namespace App\DataFixtures\Modules\Remembrall\Entity;


use App\DataFixtures\Modules\Security\Entity\UserFixtures;
use App\Modules\Remembrall\Entity\Reminder;
use DateInterval;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ReminderFixtures extends Fixture implements DependentFixtureInterface
{

    public function load(ObjectManager $manager): void
    {
//        $this->createOneTimeReminders($manager, 3);
//        $this->createCyclicReminders($manager, 4);

        for ($i = 0; $i <= 10; $i++) {
            $reminder = new Reminder();
            $reminder->setTitle('Test title');
            $reminder->setDescription('Test description');
            if ($i % 2 === 0) {
                $user = $this->getReference(UserFixtures::ADMIN_USER_REFERENCE);
            } else {
                $user = $this->getReference(UserFixtures::USER_REFERENCE);
            }
            $reminder->setUser($user);
            $delayDays = $i + 1;
            $remindAt = (new DateTime())->add(new DateInterval('P' . $delayDays . 'D'));
            $reminder->setRemindAt($remindAt);
            $reminder->setChannels(['email']);
            if ($i === 0 || $i === 1 || $i === 2 || $i === 3 || $i === 4){
                $cyclic = $this->getReference(CyclicFixtures::getReferenceKey($i));
            } else {
                $cyclic = null;
            }
            $reminder->setCyclic($cyclic);
            $manager->persist($reminder);
        }
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            CyclicFixtures::class
        ];
    }

    private function createOneTimeReminders(ObjectManager $manager, int $amount): void
    {
        for ($i = 0; $i <= $amount; $i++) {
            $reminder = new Reminder();
            $reminder->setTitle('Test One Time title');
            $reminder->setDescription('Test One Time description');
            if ($i % 2 === 0) {
                $user = $this->getReference(UserFixtures::ADMIN_USER_REFERENCE);
            } else {
                $user = $this->getReference(UserFixtures::USER_REFERENCE);
            }
            $reminder->setUser($user);
            $delayDays = $i + 1;
            $remindAt = (new DateTime())->add(new DateInterval('P' . $delayDays . 'D'));
            $reminder->setRemindAt($remindAt);
            $reminder->setChannels(['email', 'sms']);
            $manager->persist($reminder);
        }
        $manager->flush();
    }

    private function createCyclicReminders(ObjectManager $manager, int $amount): void
    {
        for ($i = 0; $i <= $amount; $i++) {
            $reminder = new Reminder();
            $reminder->setTitle('Test Cyclic title');
            $reminder->setDescription('Test Cyclic description');
            if ($i % 2 === 1) {
                $user = $this->getReference(UserFixtures::ADMIN_USER_REFERENCE);
            } else {
                $user = $this->getReference(UserFixtures::USER_REFERENCE);
            }
            $reminder->setUser($user);
            $delayDays = $i + 1;
            $remindAt = (new DateTime())->add(new DateInterval('P' . $delayDays . 'D'));
            $reminder->setRemindAt($remindAt);
            $reminder->setChannels(['email']);
            $cyclic = $this->getReference(CyclicFixtures::getReferenceKey($i % 4));
            $reminder->setCyclic($cyclic);
            $manager->persist($reminder);
        }
        $manager->flush();
    }
}
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
    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            CyclicFixtures::class,
            PreReminderFixtures::class
        ];
    }

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i <= 10; $i++) {
            $reminder = new Reminder();
            $interval = new DateInterval("P{$i}D");
            $remindAt = new DateTime();
            if ($i % 2 === 0) {
                $user = $this->getReference(UserFixtures::ADMIN_USER_REFERENCE);
                $remindAt->add($interval); // x days after today
            } else {
                $user = $this->getReference(UserFixtures::USER_REFERENCE);
                $remindAt->sub($interval); // x day before today
            }
            $reminder->setTitle('Test title');
            $reminder->setDescription('Test description');
            $reminder->setUser($user);
            $reminder->setRemindAt($remindAt);
            $reminder->setChannels(Reminder::SUPPORTED_CHANNELS);
            if (in_array($i, [0,1,2,3,4])) {
                $cyclic = $this->getReference(CyclicFixtures::getReferenceKey($i));
                $preReminder = $this->getReference(PreReminderFixtures::getReferenceKey($i));
            } else {
                $cyclic = null;
                $preReminder = null;
            }
            $reminder->setCyclic($cyclic);
            $reminder->setPreReminder($preReminder);
            $manager->persist($reminder);
            $this->addReference(self::getReferenceKey($i), $reminder);
        }
        $manager->flush();
    }

    public static function getReferenceKey(int $i): string
    {
        return sprintf('reminder_%s', $i);
    }
}
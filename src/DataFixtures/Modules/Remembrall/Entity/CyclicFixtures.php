<?php


namespace App\DataFixtures\Modules\Remembrall\Entity;


use App\Modules\Remembrall\Entity\Cyclic;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CyclicFixtures extends Fixture
{

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i <= 4; $i++) {
            $cyclic = new Cyclic();
            $cyclic->setTypeId($i + 1);
            $cyclic->setPeriodicity($i + 1);
            $manager->persist($cyclic);
            $this->addReference(self::getReferenceKey($i), $cyclic);
        }
        $manager->flush();
    }

    public static function getReferenceKey(int $i): string
    {
        return sprintf('cyclic_%s', $i);
    }
}
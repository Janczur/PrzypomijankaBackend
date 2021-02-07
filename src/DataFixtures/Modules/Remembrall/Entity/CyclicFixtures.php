<?php


namespace App\DataFixtures\Modules\Remembrall\Entity;


use App\Modules\Remembrall\Entity\Cyclic;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CyclicFixtures extends Fixture implements DependentFixtureInterface
{

    public function getDependencies(): array
    {
        return [
            CyclicTypeFixtures::class
        ];
    }

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 4; $i++) {
            $cyclic = new Cyclic();
            switch ($i) {
                case 0:
                    $type = $this->getReference(CyclicTypeFixtures::DAY_TYPE);
                    break;
                case 1:
                    $type = $this->getReference(CyclicTypeFixtures::WEEK_TYPE);
                    break;
                case 2:
                    $type = $this->getReference(CyclicTypeFixtures::MONTH_TYPE);
                    break;
                case 3:
                    $type = $this->getReference(CyclicTypeFixtures::YEAR_TYPE);
                    break;
            }
            $cyclic->setType($type);
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
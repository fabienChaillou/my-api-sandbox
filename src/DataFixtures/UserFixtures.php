<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        for($i = 1; $i < 10; $i++) {
            var_dump(sprintf("player-%d", $i));
            $user = new User(sprintf("player-%d", $i));
            $user->setCreatedAt($this->randomDate("2015-01-31", "2018-12-31"));
            $user->setUpdatedAt($this->randomDate("2015-01-31", "2018-12-31"));
            $user->setPassword($this->encoder->encodePassword($user, "player"));
            $user->setRoles(["ROLE_USER"]);
            $manager->persist($user);
        }

        $admin = new User("admin");
        $admin->setCreatedAt($this->randomDate("2015-01-31", "2018-12-31"));
        $admin->setUpdatedAt($this->randomDate("2015-01-31", "2018-12-31"));
        $admin->setPassword($this->encoder->encodePassword($admin, "admin"));
        $admin->setRoles(["ROLE_ADMIN"]);
        $manager->persist($admin);

        $manager->flush();
    }

    private function randomDate($start_date, $end_date)
    {
        $min = strtotime($start_date);
        $max = strtotime($end_date);
        $val = rand($min, $max);

        return new \DateTime(date('Y-m-d', $val));
    }
}

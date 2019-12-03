<?php

namespace App\DataFixtures;

use App\Entity\Game;
use App\Entity\Player;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * AppFixtures constructor.
     *
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager): void
    {
        $this->loadUsers($manager);
        $this->loadPlayers($manager);

    }

    /**
     * @param ObjectManager $manager
     */
    private function loadPlayers(ObjectManager $manager): void
    {
        foreach ($this->getGameData() as $i => $name) {

            $player = new Player();
            $player->setUsername($name);
            $player->setCreator($this->getReference(['tom_user', 'john_user'][0 === $i ? 0 : random_int(0, 1)]));

            foreach (range(1, 5) as $i) {
                $game = new Game();
                $game->setName($this->getfacker()->name);
                $player->addGame($game);
            }
            $manager->persist($player);
        }

        $manager->flush();
    }

    /**
     * @param ObjectManager $manager
     */
    private function loadUsers(ObjectManager $manager): void
    {
        foreach ($this->getUserData() as [$username, $password, $email, $roles]) {
            $user = new User();
            $user->setUsername($username);
            $user->setPassword($this->passwordEncoder->encodePassword($user, $password));
            $user->setEmail($email);
            $user->setRoles($roles);

            $manager->persist($user);
            $this->addReference($username, $user);
        }

        $manager->flush();
    }


    /**
     * @return array
     */
    private function getGameData(): array
    {
        $data = [];

        for($i = 1; $i <= 50; $i++) {
            $data[] = $this->getfacker()->name;
        }

        return $data;
    }

    /**
     * @return array
     */
    private function getUserData(): array
    {
        return [
            ['admin', 'admin', 'admin@test.localdev', ['ROLE_ADMIN']],
            ['tom_user', 'user', 'tom_user@test.localdev', ['ROLE_USER']],
            ['john_user', 'user', 'john_user@test.localdev', ['ROLE_USER']],
        ];
    }

    private static function getfacker()
    {
        return Factory::create();
    }
}

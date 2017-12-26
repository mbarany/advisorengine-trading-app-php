<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    const API_KEY = 'b6b576a9-010e-4e83-82a8-4d9c6d699b77';

    /**
     * @var UserPasswordEncoderInterface
     */
    private $userPasswordEncoder;

    /**
     * UserFixtures constructor.
     *
     * @param UserPasswordEncoderInterface $userPasswordEncoder
     */
    public function __construct(UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $user = (new User())
            ->setUsername('foo@example.com')
            ->setEmail('foo@example.com')
            ->setIsActive(true)
            ->setApiKey(self::API_KEY);

        $user->setPassword(
            $this->userPasswordEncoder->encodePassword($user, 'mysecretpassword')
        );

        $manager->persist($user);
        $manager->flush();
    }
}

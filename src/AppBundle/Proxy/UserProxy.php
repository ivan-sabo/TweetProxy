<?php

namespace AppBundle\Proxy;

use AppBundle\Entity\User;
use AppBundle\Service\Twitter\TwitterService;
use Doctrine\ORM\EntityManager;

class UserProxy
{
    /**
     * Entity manager
     *
     * @var EntityManager
     */
    protected $em;

    /**
     * Constructor method
     *
     * @param EntityManager $em
     * @param TwitterService $twitterService
     */
    public function __construct(EntityManager $em, TwitterService $twitterService)
    {
        $this->em = $em;
        $this->twitterService = $twitterService;
    }

    /**
     * get users
     *
     * @return User[]
     */
    public function getUsers()
    {
        $userRepository = $this->em->getRepository(User::class);

        $users = $userRepository->findAll();

        return $users;
    }

    /**
     * get user
     *
     * @param string $username
     * @return User
     */
    public function getUser($username)
    {
        $userRepository = $this->em->getRepository(User::class);
        $user = $userRepository->findOneByScreenName($username);

        /**
         * If there is no user in local db, try to fetch him from twitter
         */
        if (empty($user)) {
            $twitterUser = $this->twitterService->getUser($username);

            if (!empty($twitterUser) && isset($twitterUser->errors)) {
                return null;
            }

            $user = new User();
            $user->setId($twitterUser->id);
            $user->setName($twitterUser->name);
            $user->setScreenName($twitterUser->screen_name);
            $user->setLocation($twitterUser->location);

            $manager->persist($user);
            $manager->flush();
        }

        return $user;
    }
}

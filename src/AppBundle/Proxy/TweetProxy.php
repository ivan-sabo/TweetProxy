<?php

namespace AppBundle\Proxy;

use AppBundle\Entity\Tweet;
use AppBundle\Entity\User;
use AppBundle\Service\Twitter\TwitterService;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Response;

class TweetProxy
{
    protected $em;

    public function __construct(EntityManager $em, TwitterService $twitterService)
    {
        $this->em = $em;
        $this->twitterService = $twitterService;
    }

    public function getUserLatestTweets(User $user, $count = 20)
    {
        $twitterRepository = $this->em->getRepository(Tweet::class);

        $lastTweetInDb = $twitterRepository->findOneBy(
            array('user' => $user->getId()),
            array('id' => 'DESC')
        );

        $lastTweetId = null;

        if (!empty($lastTweetInDb)) {
            $lastTweetId = $lastTweetInDb->getId();
        }

        $tweets = $this->twitterService->getTweets($user->getScreenName(), $lastTweetId, $count);
        
        /**
         * Store in db new tweets
         */
        foreach ($tweets as $tweet) {
            $newTweet = new Tweet();
            $newTweet->setId($tweet->id);
            $newTweet->setText($tweet->text);
            $newTweet->setUser($user);

            $this->em->persist($newTweet);
        }

        $this->em->flush();

        $tweets = $twitterRepository->findBy(
            array('user' => $user->getId()),
            array('id' => 'DESC'),
            $count
        );

        return $tweets;
    }
}

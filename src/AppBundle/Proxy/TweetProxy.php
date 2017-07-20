<?php

namespace AppBundle\Proxy;

use AppBundle\Entity\Tweet;
use AppBundle\Entity\User;
use AppBundle\Service\Twitter\TwitterService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Symfony\Component\HttpFoundation\Response;

class TweetProxy
{
    /**
     * Entity manager
     *
     * @var EntityManager
     */
    protected $em;

    /**
     * Constructor methods
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
     * Get latest tweets
     *
     * @param User $user
     * @param integer $count
     * @return Tweet[]
     */
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
    
    /**
     * This method is ugly af and should be rewritten
     *
     * @param String $searchString
     * @param User $user
     * @return array
     */
    public function searchTweets(String $searchString, User $user = null)
    {
        $sql = "SELECT * FROM tweet WHERE ";

        if (!empty($searchString)) {
            $sql .= "MATCH(text) AGAINST ('$searchString' IN NATURAL LANGUAGE MODE) ";
        }

        if (!empty($searchString) && !empty($user)) {
            $sql .= 'AND ';
        }

        if (!empty($user)) {
            $sql .= " tweet.user_id =" . $user->getId();
        }
        
        $stmt = $this->em->getConnection()->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}

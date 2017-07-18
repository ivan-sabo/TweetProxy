<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Proxy\UserProxy;
use AppBundle\Proxy\TweetProxy;

class TwitterController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(UserProxy $userProxy)
    {
        $users = $userProxy->getUsers();
        /**
         * @todo Connect with template
         */
        return new Response(json_encode($users));
    }

    /**
     * @Route("/{username}", name="userTweets")
     */
    public function userTweets(UserProxy $userProxy, TweetProxy $tweetProxy, $username)
    {
        $tweetsCount = $this->container->getParameter('tweets_number');

        $user = $userProxy->getUser($username);
        
        if (empty($user)) {
            return new Response('There is no user with username:' . $username);
        }

        $tweets = $tweetProxy->getUserLatestTweets($user, $tweetsCount);

        return new Response(json_encode(count($tweets)));
    }
}

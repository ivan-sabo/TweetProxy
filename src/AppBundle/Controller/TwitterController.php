<?php

namespace AppBundle\Controller;

use AppBundle\Proxy\TweetProxy;
use AppBundle\Proxy\UserProxy;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class TwitterController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(UserProxy $userProxy)
    {
        $users = $userProxy->getUsers();
        
        return $this->render('default/index.html.twig', array('users' => $users));
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

        return $this->render('default/user.html.twig', array('user' => $user, 'tweets' => $tweets));
    }
}

<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Service\Twitter\TwitterService;
use AppBundle\Entity\Tweet;
use AppBundle\Entity\User;

class TwitterController extends Controller
{
    private $tweetRepository;

    private $userRepository;
    
    public function __construct()
    {
    }

    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request, TwitterService $twitter)
    {
        /**
         * display users from db
         */
        $userRepository = $this->getDoctrine()->getRepository(User::class);

        $users = $userRepository->findAll();

        /**
         * @todo Connect with template
         */
        return new Response(json_encode($users));
    }

    /**
     * @Route("/{username}", name="getTwitterUser")
     */
    public function getTwitterUser(Request $request)
    {
        /**
         * display user and last 20 tweets
         *
         * if there is no user in db, get him from twitter
         *
         * fetch tweets tweeted from last tweet_id, store them in db, and get last 20 tweets
         */
        //$user = $twitter->getUser('ivsabo');

        $tweets = $twitter->getTweets('ivsabo');

        return new Response(json_encode($tweets));
    }
}

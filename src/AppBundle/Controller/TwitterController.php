<?php

namespace AppBundle\Controller;

use AppBundle\Proxy\TweetProxy;
use AppBundle\Proxy\UserProxy;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Tweet;

class TwitterController extends Controller
{
    /**
     * @Route("/", name="homepage")
     *
     * @param UserProxy $userProxy
     * @return Response
     */
    public function indexAction(UserProxy $userProxy)
    {
        $users = $userProxy->getUsers();
        
        return $this->render('default/index.html.twig', array('users' => $users));
    }

    /**
     * @Method({"GET"})
     * @Route("/search", name="getSearchTweets")
     *
     * @param UserProxy $userProxy
     * @return Response
     */
    public function getSearchTweets(UserProxy $userProxy)
    {
        $users = $userProxy->getUsers();

        return $this->render('default/search.html.twig', array('users' => $users, 'tweets' => null));
    }

    /**
     * @Method({"POST"})
     * @Route("/search", name="postSearchTweets")
     *
     * @param Request $request
     * @param UserProxy $userProxy
     * @param TweetProxy $tweetProxy
     * @return Response
     */
    public function postSearchTweets(Request $request, UserProxy $userProxy, TweetProxy $tweetProxy)
    {
        $screenName = $request->request->get('screenname');
        $query = $request->request->get('query');

        $user = $userProxy->getUser($screenName);

        $tweets = $tweetProxy->searchTweets($query, $user);
        $users = $userProxy->getUsers();

        return $this->render('default/search.html.twig', array('users' => $users, 'tweets' => $tweets));
    }

    /**
     * @Route("/{username}", name="userTweets")
     *
     * @param UserProxy $userProxy
     * @param TweetProxy $tweetProxy
     * @param string $username
     * @return Response
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

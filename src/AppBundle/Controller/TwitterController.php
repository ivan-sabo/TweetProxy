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
    public function getTwitterUser(Request $request, TwitterService $twitter, $username)
    {
        /**
         * display user and last 20 tweets
         *
         * if there is no user in db, get him from twitter
         *
         * fetch tweets tweeted from last tweet_id, store them in db, and get last 20 tweets
         */
        
        /**
         * @todo move this into config
         */
        $countTweets = 20;

        $userRepository = $this->getDoctrine()->getRepository(User::class);
        $user = $userRepository->findOneByScreenName($username);

        if (empty($user)) {
            $twitterUser = $twitter->getUser($username);

            if (!empty($twitterUser) && isset($twitterUser->errors)) {
                return new Response('User can\'t be fetched');
            }

            $manager = $this->getDoctrine()->getManager();

            /**
             * @todo Create adapter, connect db user entity with twitter user object, and move this there
             */
            $user = new User();
            $user->setId($twitterUser->id);
            $user->setName($twitterUser->name);
            $user->setScreenName($twitterUser->screen_name);
            $user->setLocation($twitterUser->location);

            $manager->persist($user);
            $manager->flush();

            return new Response("Added new user with username {$user->getScreenName()}");
        }

        $twitterRepository = $this->getDoctrine()->getRepository(Tweet::class);
        $tweets = $twitterRepository->findBy(
            array('user' => $user->getId()),
            array('id' => 'DESC')
        );

        if (empty($tweets)) {
            $tweets = $twitter->getTweets($user->getScreenName(), null, $countTweets);

            return new Response(json_encode($tweets));

            /**
             * @todo store new tweets into db
             */
        }

        /**
         * @todo get last tweet from db, check api if there is new tweets and store them if there are any
         */

        /**
         * @todo return user with latest tweets
         */
        return new Response(json_encode(count($tweets)));
    }
}

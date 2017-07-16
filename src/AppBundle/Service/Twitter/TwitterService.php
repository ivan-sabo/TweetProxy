<?php

namespace AppBundle\Service\Twitter;

use AppBundle\Service\Twitter\Entities\User;
use Abraham\TwitterOAuth\TwitterOAuth;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class TwitterService
{
    /**
     *
     * @var TwitterOAuth
     */
    private $twitter;

    /**
     *
     * @var Serializer
     */
    private $serializer;

    /**
     * @todo move keys into config file
     */

    private $apiKey = 'H35qfKtUaP7AdM4ybpIJ24iBe';

    private $secretKey = 'MUvLXwlDoxh4e2eSbpjd3tWmJhyUraJ8WMKT22URHp5A6TklcC';
    
    public function __construct()
    {
        $this->twitter = new TwitterOAuth($this->apiKey, $this->secretKey);
        $this->serializer = new Serializer([new ObjectNormalizer], []);
    }

    public function getUser($username)
    {
        $response = $this->twitter->get("/users/show", ['screen_name' => $username]);

        return $response;
    }

    public function getTweets($screen_name, $since_id = null, $count = null)
    {
        $options = ['screen_name' => $screen_name];

        if (!empty($since_id)) {
            $options['since_id'] = $since_id;
        }

        if (!empty($count)) {
            $options['count'] = $count;
        }

        $response = $this->twitter->get("/statuses/user_timeline", $options);

        return $response;
    }
}

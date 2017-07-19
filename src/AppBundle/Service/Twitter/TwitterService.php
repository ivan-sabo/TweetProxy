<?php

namespace AppBundle\Service\Twitter;

use AppBundle\Service\Twitter\Entities\User;
use Abraham\TwitterOAuth\TwitterOAuth;

class TwitterService
{
    /**
     * @var TwitterOAuth
     */
    private $twitter;
    
    public function __construct($apiKey, $secretKey)
    {
        $this->twitter = new TwitterOAuth($apiKey, $secretKey);
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

<?php
namespace Riskio\OAuth2\Client\Provider;

use League\OAuth2\Client\Entity\User;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Token\AccessToken;

class Auth0 extends AbstractProvider
{
    public $responseType = 'json';

    public $authorizationHeader = 'Bearer';

    public $account;

    protected function domain()
    {
        if (empty($this->account)) {
            throw new \RuntimeException('Auth0 account is not specified');
        }

        return 'https://' . $this->account . '.auth0.com';
    }

    public function urlAuthorize()
    {
        return $this->domain() . '/authorize';
    }

    public function urlAccessToken()
    {
        return $this->domain() . '/oauth/token';
    }

    public function urlUserDetails(AccessToken $token)
    {
        return $this->domain() . '/userinfo';
    }

    public function userDetails($response, AccessToken $token)
    {
        $user = new User();

        $imageUrl = isset($response->picture) ? $response->picture : null;

        $user->exchangeArray([
            'uid'      => $this->userUid($response, $token),
            'nickname' => $response->nickname,
            'name'     => $this->userScreenName($response, $token),
            'email'    => $this->userEmail($response, $token),
            'imageUrl' => $imageUrl,
        ]);

        return $user;
    }

    public function userUid($response, AccessToken $token)
    {
        return $response->user_id;
    }
}

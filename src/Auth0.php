<?php
namespace Riskio\OAuth2\Client\Provider;

use League\OAuth2\Client\Entity\User;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Token\AccessToken;
use Psr\Http\Message\ResponseInterface;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;

class Auth0 extends AbstractProvider
{


    protected $account;

    protected $authorizationHeader;

    public function __construct(array $options = [], array $collaborators = [])
    {
        parent::__construct($options, $collaborators);
        $this->account = isset($options['account'])? $options['account'] : null;
    }

    protected function domain()
    {
        if (empty($this->account)) {
            throw new \RuntimeException('Auth0 account is not specified');
        }

        return 'https://' . $this->account . '.auth0.com';
    }

    public function getBaseAuthorizationUrl()
    {
        return $this->domain() . '/authorize';
    }

    public function getBaseAccessTokenUrl(array $params = [])
    {
        return $this->domain() . '/oauth/token';
    }

    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        return $this->domain() . '/userinfo';
    }

    public function getDefaultScopes()
    {
        return ['openid','email'];
    }

    public function checkResponse(ResponseInterface $response, $data)
    {
        if (!empty($data['error'])) {
            $message = $data['error'].': '.$data['error_description'];
            throw new IdentityProviderException($message, null, $data);
        }
    }

    public function createResourceOwner(array $response, AccessToken $token)
    {
        return new Auth0User($response);
    }


    public function userUid($response, AccessToken $token)
    {
        return $response->user_id;
    }

    protected function getAuthorizationHeaders($token = null)
    {
        $header = !is_null($token)?
            ['Authorization'=>$token->getValues()['token_type'].' '.$token->getToken()]: null;
        return $header;
    }


}

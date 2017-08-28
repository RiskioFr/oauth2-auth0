<?php
namespace Riskio\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Riskio\OAuth2\Client\Provider\Exception\Auth0IdentityProviderException;
use Psr\Http\Message\ResponseInterface;

class Auth0 extends AbstractProvider
{
    use BearerAuthorizationTrait;

    protected $account;

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
        return ['openid', 'email'];
    }

    protected function checkResponse(ResponseInterface $response, $data)
    {
        if ($response->getStatusCode() >= 400) {
            return Auth0IdentityProviderException::fromResponse(
                $response,
                $data['error'] ?: $response->getReasonPhrase()
            );
        }
    }

    protected function createResourceOwner(array $response, AccessToken $token)
    {
        return new Auth0ResourceOwner($response);
    }
}

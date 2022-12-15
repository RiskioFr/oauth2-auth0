<?php
namespace Riskio\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;
use Riskio\OAuth2\Client\Provider\Exception\AccountNotProvidedException;
use Riskio\OAuth2\Client\Provider\Exception\Auth0IdentityProviderException;
use Riskio\OAuth2\Client\Provider\Exception\InvalidRegionException;

class Auth0 extends AbstractProvider
{
    use BearerAuthorizationTrait;

    const REGION_US = 'us';
    const REGION_EU = 'eu';
    const REGION_AU = 'au';
    const REGION_JP = 'jp';

    protected $availableRegions = [self::REGION_US, self::REGION_EU, self::REGION_AU, self::REGION_JP];

    protected $region = self::REGION_US;

    protected $account;

    protected $customDomain;

    protected function domain()
    {
        if ($this->customDomain !== null) {
            return $this->customDomain;
        }

        if (empty($this->account)) {
            throw new AccountNotProvidedException();
        }
        if (!in_array($this->region, $this->availableRegions)) {
            throw new InvalidRegionException();
        }

        $domain = 'auth0.com';

        if ($this->region !== self::REGION_US) {
            $domain = $this->region . '.' . $domain;
        }

        return $this->account . '.' . $domain;
    }

    protected function baseUrl()
    {
        return 'https://' . $this->domain();
    }

    public function getBaseAuthorizationUrl(): string
    {
        return $this->baseUrl() . '/authorize';
    }

    public function getBaseAccessTokenUrl(array $params = []): string
    {
        return $this->baseUrl() . '/oauth/token';
    }

    public function getResourceOwnerDetailsUrl(AccessToken $token): string
    {
        return $this->baseUrl() . '/userinfo';
    }

    /**
     * @return array<int,string>
     */
    public function getDefaultScopes(): array
    {
        return ['openid', 'email'];
    }

    protected function checkResponse(ResponseInterface $response, $data): void
    {
        if ($response->getStatusCode() >= 400) {
            throw Auth0IdentityProviderException::fromResponse(
                $response,
                $data['error'] ?: $response->getReasonPhrase()
            );
        }
    }

    protected function createResourceOwner(array $response, AccessToken $token): ResourceOwnerInterface
    {
        return new Auth0ResourceOwner($response);
    }

    protected function getScopeSeparator(): string
    {
        return ' ';
    }
}

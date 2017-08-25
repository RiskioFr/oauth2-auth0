<?php
namespace Riskio\OAuth2\Client\Test\Provider;

use Riskio\OAuth2\Client\Provider\Auth0 as OauthProvider;

class Auth0Test extends \PHPUnit_Framework_TestCase
{
    protected $config = [
        'account'      => 'mock_account',
        'clientId'     => 'mock_client_id',
        'clientSecret' => 'mock_secret',
        'redirectUri'  => 'none',
    ];

    public function testGetAuthorizationUrl()
    {
        $provider = new OauthProvider($this->config);
        $url = $provider->getAuthorizationUrl();
        $uri = parse_url($url);

        $this->assertEquals($this->config['account'] . '.auth0.com', $uri['host']);
        $this->assertEquals('/authorize', $uri['path']);
    }

    public function testGetAuthorizationUrlWhenAccountIsNotSpecifiedShouldThrowException()
    {
        unset($this->config['account']);

        $provider = new OauthProvider($this->config);

        $this->setExpectedException('RuntimeException');
        $provider->getAuthorizationUrl();
    }

    public function testGetUrlAccessToken()
    {
        $provider = new OauthProvider($this->config);
        $url = $provider->getBaseAccessTokenUrl();
        $uri = parse_url($url);

        $this->assertEquals($this->config['account'] . '.auth0.com', $uri['host']);
        $this->assertEquals('/oauth/token', $uri['path']);
    }

    public function testGetAccessTokenUrlWhenAccountIsNotSpecifiedShouldThrowException()
    {
        unset($this->config['account']);

        $provider = new OauthProvider($this->config);

        $this->setExpectedException('RuntimeException');
        $provider->getBaseAccessTokenUrl();
    }

    public function testGetUrlUserDetails()
    {
        $provider = new OauthProvider($this->config);

        $accessTokenDummy = $this->getAccessToken();

        $url = $provider->getResourceOwnerDetailsUrl($accessTokenDummy);
        $uri = parse_url($url);

        $this->assertEquals($this->config['account'] . '.auth0.com', $uri['host']);
        $this->assertEquals('/userinfo', $uri['path']);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testGetUserDetailsUrlWhenAccountIsNotSpecifiedShouldThrowException()
    {
        unset($this->config['account']);

        $provider = new OauthProvider($this->config);

        $accessTokenDummy = $this->getAccessToken();
        $userDetails = $provider->getResourceOwner($accessTokenDummy);
        $this->setExpectedException('RuntimeException');

    }


    private function getAccessToken()
    {
        return $this->getMockBuilder('League\OAuth2\Client\Token\AccessToken')
            ->disableOriginalConstructor()
            ->getMock();
    }

}

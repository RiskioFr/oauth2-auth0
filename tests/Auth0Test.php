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
        $url = $provider->urlAuthorize();
        $uri = parse_url($url);

        $this->assertEquals($this->config['account'] . '.auth0.com', $uri['host']);
        $this->assertEquals('/authorize', $uri['path']);
    }

    public function testGetAuthorizationUrlWhenAccountIsNotSpecifiedShouldThrowException()
    {
        unset($this->config['account']);

        $provider = new OauthProvider($this->config);

        $this->setExpectedException('RuntimeException');
        $provider->urlAuthorize();
    }

    public function testGetUrlAccessToken()
    {
        $provider = new OauthProvider($this->config);
        $url = $provider->urlAccessToken();
        $uri = parse_url($url);

        $this->assertEquals($this->config['account'] . '.auth0.com', $uri['host']);
        $this->assertEquals('/oauth/token', $uri['path']);
    }

    public function testGetAccessTokenUrlWhenAccountIsNotSpecifiedShouldThrowException()
    {
        unset($this->config['account']);

        $provider = new OauthProvider($this->config);

        $this->setExpectedException('RuntimeException');
        $provider->urlAccessToken();
    }

    public function testGetUrlUserDetails()
    {
        $provider = new OauthProvider($this->config);

        $accessTokenDummy = $this->getAccessToken();

        $url = $provider->urlUserDetails($accessTokenDummy);
        $uri = parse_url($url);

        $this->assertEquals($this->config['account'] . '.auth0.com', $uri['host']);
        $this->assertEquals('/userinfo', $uri['path']);
    }

    public function testGetUserDetailsUrlWhenAccountIsNotSpecifiedShouldThrowException()
    {
        unset($this->config['account']);

        $provider = new OauthProvider($this->config);

        $accessTokenDummy = $this->getAccessToken();

        $this->setExpectedException('RuntimeException');
        $provider->urlUserDetails($accessTokenDummy);
    }

    public function getUserDetailsDataProvider()
    {
        return [
            [
                [
                    'user_id'  => 123,
                    'nickname' => 'mock_nickname',
                ],
                [
                    'uid'      => 123,
                    'nickname' => 'mock_nickname',
                    'name'     => null,
                    'email'    => null,
                    'imageUrl' => null,
                ],
            ],
            [
                [
                    'user_id'  => 123,
                    'nickname' => 'mock_nickname',
                    'name'     => 'mock_name',
                    'email'    => 'mock_email',
                    'picture'  => 'mock_picture',
                ],
                [
                    'uid'      => 123,
                    'nickname' => 'mock_nickname',
                    'name'     => 'mock_name',
                    'email'    => 'mock_email',
                    'imageUrl' => 'mock_picture',
                ],
            ],
        ];
    }

    /**
     * @dataProvider getUserDetailsDataProvider
     */
    public function testGetUserDetails($responseData, $expectedUserData)
    {
        $response = (object) $responseData;

        $provider = new OauthProvider($this->config);

        $accessTokenDummy = $this->getAccessToken();
        $userDetails = $provider->userDetails($response, $accessTokenDummy);

        $this->assertInstanceOf('League\OAuth2\Client\Entity\User', $userDetails);

        $this->assertObjectHasAttribute('uid', $userDetails);
        $this->assertObjectHasAttribute('nickname', $userDetails);
        $this->assertObjectHasAttribute('name', $userDetails);
        $this->assertObjectHasAttribute('email', $userDetails);
        $this->assertObjectHasAttribute('imageUrl', $userDetails);

        $this->assertSame($expectedUserData['uid'], $userDetails->uid);
        $this->assertSame($expectedUserData['nickname'], $userDetails->nickname);
        $this->assertSame($expectedUserData['name'], $userDetails->name);
        $this->assertSame($expectedUserData['email'], $userDetails->email);
        $this->assertSame($expectedUserData['imageUrl'], $userDetails->imageUrl);
    }

    public function testGetUserUid()
    {
        $response = new \stdClass();
        $response->user_id = 123;

        $provider = new OauthProvider($this->config);

        $accessTokenDummy = $this->getAccessToken();
        $userUid = $provider->userUid($response, $accessTokenDummy);

        $this->assertSame($response->user_id, $userUid);
    }

    private function getAccessToken()
    {
        return $this->getMockBuilder('League\OAuth2\Client\Token\AccessToken')
            ->disableOriginalConstructor()
            ->getMock();
    }
}

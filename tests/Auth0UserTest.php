<?php
namespace Riskio\OAuth2\Client\Test\Provider;

class Auth0UserTest extends \PHPUnit_Framework_TestCase{


    public function getUserDetailsDataProvider()
    {
        return [
            'email' => 'testuser@gmail.com',
            'email_verified' => true,
            'name' => 'Test User',
            'given_name' => 'Test',
            'family_name' => 'User',
            'picture' => 'https://lh5.googleusercontent.com/-NNasdfdfasdf/asfadfdf/photo.jpg',
            'gender' => 'male',
            'locale' => 'en-GB',
            'clientID' => 'U_DUMmyClientIdhere',
            'updated_at' => '2017-08-25T10:54:21.326Z',
            'user_id' => 'google-oauth2|11204527450454',
            'nickname' => ' testuser',
            'identities' =>
                [
                    0 =>
                        [
                            'provider' => 'google-oauth2',
                            'user_id' => '11204527450454',
                            'connection' => 'google-oauth2',
                            'isSocial' => true,
                        ],
                ],
            'created_at' => '2017-08-14T13:22:29.753Z',
            'sub' => 'google-oauth2|113974520365241488704',
        ];
    }

    /**
     * @dataProvider getUserDetailsDataProvider
     */
    public function testGetUserDetails($responseData, $expectedUserData)
    {

    }
}
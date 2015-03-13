# Auth0 Provider for OAuth 2.0 Client

[![Build Status](https://img.shields.io/travis/RiskioFr/oauth2-auth0.svg)](https://travis-ci.org/RiskioFr/oauth2-auth0)
[![License](https://img.shields.io/packagist/l/riskio/oauth2-auth0.svg)](https://github.com/RiskioFr/oauth2-auth0/blob/master/LICENSE)
[![Latest Stable Version](https://img.shields.io/packagist/v/riskio/oauth2-auth0.svg)](https://packagist.org/packages/riskio/oauth2-auth0)

This package provides Auth0 OAuth 2.0 support for the PHP League's [OAuth 2.0 Client](https://github.com/thephpleague/oauth2-client).

## Installation

To install, use composer:

```
composer require riskio/oauth2-auth0
```

## Usage

Usage is the same as The League's OAuth client, using `Riskio\OAuth2\Client\Provider\Auth0` as the provider.

### Authorization Code Flow

```php
$provider = new Riskio\OAuth2\Client\Provider\Auth0([
    'clientId'     => '{auth0-client-id}',
    'clientSecret' => '{auth0-client-secret}',
    'redirectUri'  => 'https://example.com/callback-url'
]);

if (!isset($_GET['code'])) {

    // If we don't have an authorization code then get one
    $authUrl = $provider->getAuthorizationUrl();
    $_SESSION['oauth2state'] = $provider->state;
    header('Location: ' . $authUrl);
    exit;

// Check given state against previously stored one to mitigate CSRF attack
} elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {

    unset($_SESSION['oauth2state']);
    exit('Invalid state');

} else {

    // Try to get an access token (using the authorization code grant)
    $token = $provider->getAccessToken('authorization_code', [
        'code' => $_GET['code']
    ]);

    // Optional: Now you have a token you can look up a users profile data
    try {

        // We got an access token, let's now get the user's details
        $userDetails = $provider->getUserDetails($token);

        // Use these details to create a new profile
        printf('Hello %s!', $userDetails->firstName);

    } catch (Exception $e) {

        // Failed to get user details
        exit('Oh dear...');
    }

    // Use this to interact with an API on the users behalf
    echo $token->accessToken;
}
```

## Refreshing a Token

Auth0's OAuth implementation does not use refresh tokens.

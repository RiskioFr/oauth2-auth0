<?php

namespace Riskio\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;

class Auth0User implements ResourceOwnerInterface{

    /**
     * @var array
     */
    protected $data;

    public function __construct(array $response)
    {
        $this->data = $response;

    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getField('user_id');
    }

    /**
     * @inheritdoc
     */
    public function toArray()
    {
        return $this->data;
    }

    /**
     * Get's user's email address if available
     * @return string|null
     */
    public function getEmail(){
        return $this->getField('email');
    }

    /**
     * Returns full name as returned by provider
     * @return string|null
     */
    public function getName(){
        return $this->getField('name');
    }

    /**
     * Returns Auth0 Identities
     * @see https://auth0.com/docs/user-profile/user-profile-structure
     * @return array|null
     */
    public function getIdentities(){
        return $this->getField('identities');
    }

    /**
     * Returns nickname if available
     * @return string|null
     */
    public function getNickname(){
        return $this->getField('nickname');
    }

    private function getField($key)
    {
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }


}
<?php
namespace Riskio\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use League\OAuth2\Client\Tool\ArrayAccessorTrait;

class Auth0ResourceOwner implements ResourceOwnerInterface
{
    use ArrayAccessorTrait;

    /**
     * @var array
     */
    protected $response;

    public function __construct(array $response = [])
    {
        $this->response = $response;
    }

    public function getId()
    {
        return $this->getValueByKey($this->response, 'user_id');
    }

    public function getEmail()
    {
        return $this->getValueByKey($this->response, 'email');
    }

    public function getName()
    {
        return $this->getValueByKey($this->response, 'name');
    }

    public function getNickname()
    {
        return $this->getValueByKey($this->response, 'nickname');
    }

    public function getIdentities()
    {
        return $this->getValueByKey($this->response, 'identities');
    }

    public function getPictureUrl()
    {
        return $this->getValueByKey($this->response, 'picture');
    }

    public function toArray()
    {
        return $this->response;
    }
}

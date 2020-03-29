<?php
namespace Lucinda\Framework\OAuth2\Yahoo;

use Lucinda\Framework\OAuth2\AbstractUserInformation;

/**
 * Collects information about logged in Yahoo user
 */
class UserInformation extends AbstractUserInformation
{
    /**
     * Saves logged in user details received from Yahoo.
     *
     * @param array $info
     */
    public function __construct(array $info)
    {
        $this->id = $info["profile"]["guid"];
        $this->name = $info["profile"]["familyName"]." ".$info["profile"]["givenName"];
        $this->email = $info["profile"]["emails"]["handle"];
    }
}

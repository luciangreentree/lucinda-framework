<?php
namespace Lucinda\Framework\OAuth2\Google;

use Lucinda\Framework\OAuth2\AbstractUserInformation;

/**
 * Collects information about logged in Google user
 */
class UserInformation extends AbstractUserInformation
{
    /**
     * Saves logged in user details received from Google.
     *
     * @param string[string] $info
     */
    public function __construct(array $info)
    {
        $this->id = $info["id"];
        $this->name = $info["displayName"];
        $this->email = (!empty($info["emails"][0]["value"])?$info["emails"][0]["value"]:"");
    }
}

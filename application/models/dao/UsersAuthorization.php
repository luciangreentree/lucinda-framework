<?php
/**
 * DAO to use when user rights are to be checked in database
 * TODO: userroles
 */
class UsersAuthorization extends Lucinda\WebSecurity\Authorization\DAO\UserAuthorizationDAO
{
    /**
     * {@inheritDoc}
     * @see \Lucinda\WebSecurity\Authorization\DAO\UserAuthorizationDAO::isAllowed()
     */
    public function isAllowed(\Lucinda\WebSecurity\Authorization\DAO\PageAuthorizationDAO $page, string $httpRequestMethod): bool
    {
        return SQL("{QUERY}", array(":user"=>$this->userID, ":resource"=>$page->getID()))->toValue();
    }
}

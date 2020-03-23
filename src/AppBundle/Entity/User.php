<?php
namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;

class User extends BaseUser
{
    /**
     * @var string
     */
    private $real_name;

    /**
     * @var string
     */
    private $real_surname;


    /**
     * Set realName
     *
     * @param string $realName
     *
     * @return User
     */
    public function setRealName($realName)
    {
        $this->real_name = $realName;

        return $this;
    }

    /**
     * Get realName
     *
     * @return string
     */
    public function getRealName()
    {
        return $this->real_name;
    }

    /**
     * Set realSurname
     *
     * @param string $realSurname
     *
     * @return User
     */
    public function setRealSurname($realSurname)
    {
        $this->real_surname = $realSurname;

        return $this;
    }

    /**
     * Get realSurname
     *
     * @return string
     */
    public function getRealSurname()
    {
        return $this->real_surname;
    }

    public function __construct()
    {
        parent::__construct();
        // your own logic
    }
}

<?php
//UserBundle\Entity\User.php
namespace UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

class User implements UserInterface, \Serializable
{
    private $id;
    private $username;
    private $real_name;
    private $real_surname;
    private $email;
    private $password;
	private $registration_date;
	private $last_login;
	
	
	public function getRoles() {
		return array('ROLE_USER');
	}
	
	public function getPassword() {
		return $this->password;
	}
	
	public function getSalt() {
		// you *may* need a real salt depending on your encoder
		// see section on salt below
		return null;
	}
	
	public function getUsername() {
		return $this->username;
	}
	
	public function eraseCredentials() {}
	
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt,
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt
        ) = unserialize($serialized);
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Set real_name
     *
     * @param string $realName
     * @return User
     */
    public function setRealName($realName)
    {
        $this->real_name = $realName;

        return $this;
    }

    /**
     * Get real_name
     *
     * @return string 
     */
    public function getRealName()
    {
        return $this->real_name;
    }

    /**
     * Set real_surname
     *
     * @param string $realSurname
     * @return User
     */
    public function setRealSurname($realSurname)
    {
        $this->real_surname = $realSurname;

        return $this;
    }

    /**
     * Get real_surname
     *
     * @return string 
     */
    public function getRealSurname()
    {
        return $this->real_surname;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Set registration_date
     *
     * @param \DateTime $registrationDate
     * @return User
     */
    public function setRegistrationDate($registrationDate)
    {
        $this->registration_date = $registrationDate;

        return $this;
    }

    /**
     * Get registration_date
     *
     * @return \DateTime 
     */
    public function getRegistrationDate()
    {
        return $this->registration_date;
    }

    /**
     * Set last_login
     *
     * @param \DateTime $lastLogin
     * @return User
     */
    public function setLastLogin($lastLogin)
    {
        $this->last_login = $lastLogin;

        return $this;
    }

    /**
     * Get last_login
     *
     * @return \DateTime 
     */
    public function getLastLogin()
    {
        return $this->last_login;
    }
}

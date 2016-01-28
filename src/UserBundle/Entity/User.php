<?php
//UserBundle\Entity\User.php
namespace UserBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;

class User extends BaseUser
{
	protected $id;
	protected $real_name;
	protected $real_surname;
	protected $terms_accepted;
	
	public function __construct() 
	{
		parent::__construct();
		// your own logic
	}
	
	public function getId() 
	{
		return $this->id;
	}
	
    public function setRealName($real_name)
    {
        $this->real_name = $real_name;
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
    public function setRealSurname($real_surname)
    {
        $this->real_surname = $real_surname;
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
    
    public function getTermsAccepted()
    {
    	return $this->terms_accepted;
    }
    
    public function setTermsAccepted($terms_accepted)
    {
    	$this->terms_accepted = (bool) $terms_accepted;
    }
}


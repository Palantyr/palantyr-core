<?php
// UserBundle/Form/Model/Registration.php
namespace UserBundle\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;

//use UserBundle\Entity\User;

//Sin uso ahora mismo.
class Registration
{
	//private $user;
	protected $terms_accepted;

	/*
	public function setUser(User $user)
	{
		$this->user = $user;
	}

	public function getUser()
	{
		return $this->user;
	}
	*/
	
	public function getTermsAccepted()
	{
		return $this->terms_accepted;
	}

	public function setTermsAccepted($terms_accepted)
	{
		$this->terms_accepted = (bool) $terms_accepted;
	}
}
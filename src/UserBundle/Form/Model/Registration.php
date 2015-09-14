<?php
// UserBundle/Form/Model/Registration.php
namespace UserBundle\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;

use UserBundle\Entity\User;

class Registration
{
	private $user;
	private $termsAccepted;

	public function setUser(User $user)
	{
		$this->user = $user;
	}

	public function getUser()
	{
		return $this->user;
	}

	public function getTermsAccepted()
	{
		return $this->termsAccepted;
	}

	public function setTermsAccepted($termsAccepted)
	{
		$this->termsAccepted = (bool) $termsAccepted;
	}
}
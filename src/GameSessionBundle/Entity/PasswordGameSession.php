<?php
//GameSessionBundle\Entity\PasswordGameSesion.php
namespace GameSessionBundle\Entity;

class PasswordGameSession 
{
	private $password;

    /**
     * Set password
     *
     * @param string $password
     * @return GameSession
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }
}

<?php

namespace AppBundle\Entity;

/**
 * RolGameRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class RolGameRepository extends \Doctrine\ORM\EntityRepository
{
    public function findAll()
    {
        $query = $this->getEntityManager()
            ->createQuery(
                'SELECT rolGame FROM GameSessionBundle:RolGame rolGame'
            );

        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

    public function findAllActives()
    {
        $query = $this->getEntityManager()
            ->createQuery(
                'SELECT rolGame FROM GameSessionBundle:RolGame rolGame
				 WHERE rolGame.active = 1'
            );

        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
}

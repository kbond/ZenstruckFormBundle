<?php

namespace Zenstruck\Bundle\FormBundle\Form;

use Doctrine\Persistence\ManagerRegistry;
use Zend\Crypt\BlockCipher;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class AjaxEntityManager
{
    protected $registry;
    protected $secret;

    public function __construct(ManagerRegistry $registry, $secret)
    {
        $this->registry = $registry;
        $this->secret = $secret;
    }

    public function findEntitiesByMethod($entity, $method, $query, $extra = array())
    {
        $className = $this->decriptString($entity);
        $method = $this->decriptString($method);

        try {
            $repo = $this->registry->getRepository($className);
        } catch (\ErrorException $e) {
            throw new \InvalidArgumentException('Entity does not exist');
        }

        if (!method_exists($repo, $method)) {
            throw new \InvalidArgumentException(sprintf(
                'The method "%s" for "%s" does not exist.',
                $method,
                get_class($repo)
            ));
        }

        return $repo->$method($query, $extra);
    }

    public function findEntitiesByProperty($entity, $property, $query)
    {
        $className = $this->decriptString($entity);
        $property = $this->decriptString($property);

        $sql = "SELECT e.id, e.$property AS text FROM $className e WHERE e.$property LIKE :query";

        $em = $this->registry->getManager();
        $dqlQuery = $em->createQuery($sql);
        $dqlQuery->setParameter('query', '%'.$query.'%');
        $dqlQuery->setMaxResults(10);

        return $dqlQuery->getResult();
    }

    public function encriptString($string)
    {
        return $this->getBlockCipher()->encrypt($string);
    }

    public function decriptString($string)
    {
        return $this->getBlockCipher()->decrypt($string);
    }

    /**
     * @return \Zend\Crypt\BlockCipher
     */
    protected function getBlockCipher()
    {
        if (!class_exists('\Zend\Crypt\BlockCipher')) {
            throw new \Exception('zendframework/zend-crypt must be installed to use the ajax_entity_controller feature.');
        }

        $blockCipher = BlockCipher::factory('mcrypt', array('algo' => 'aes'));
        $blockCipher->setKey($this->secret);

        return $blockCipher;
    }
}

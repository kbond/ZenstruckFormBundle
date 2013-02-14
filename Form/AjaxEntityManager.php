<?php

namespace Zenstruck\Bundle\FormBundle\Form;

use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class AjaxEntityManager
{
    protected $registry;
    protected $secret;
    protected $controllerEnabled;

    public function __construct(ManagerRegistry $registry, $secret, $controllerEnabled = false)
    {
        $this->registry = $registry;
        $this->secret = $secret;
        $this->controllerEnabled = $controllerEnabled;
    }

    public function isControllerEnabled()
    {
        return $this->controllerEnabled;
    }

    public function findEntitiesByMethod($entity, $method, $query)
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

        return $repo->$method($query);
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

    /**
     * Encrypt code src: http://blog.justin.kelly.org.au/simple-mcrypt-encrypt-decrypt-functions-for-p/
     */
    public function encriptString($string)
    {
        return trim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $this->secret, $string, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND))));
    }

    /**
     * Decrypt code src: http://blog.justin.kelly.org.au/simple-mcrypt-encrypt-decrypt-functions-for-p/
     */
    public function decriptString($string)
    {
        return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $this->secret, base64_decode($string), MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND)));
    }
}
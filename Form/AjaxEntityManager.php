<?php

namespace Zenstruck\Bundle\FormBundle\Form;

use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Routing\Router;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class AjaxEntityManager
{
    protected $registry;
    protected $router;
    protected $secret;
    protected $controllerEnabled;

    public function __construct(ManagerRegistry $registry, Router $router, $secret, $controllerEnabled = false)
    {
        $this->registry = $registry;
        $this->router = $router;
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

    public function generatePropertyUrl($className, $property)
    {
        return $this->router->generate('zenstruck_ajax_entity_property', array(
                'entity' => $this->encriptString($className),
                'property' => $this->encriptString($property)
            ));
    }

    public function generateMethodUrl($className, $method)
    {
        return $this->router->generate('zenstruck_ajax_entity_method', array(
                'entity' => $this->encriptString($className),
                'method' => $this->encriptString($method)
            ));
    }

    public function encriptString($string)
    {
        return base64_encode($string.$this->secret);
    }

    public function decriptString($string)
    {
        return str_replace($this->secret, '', base64_decode($string));
    }
}
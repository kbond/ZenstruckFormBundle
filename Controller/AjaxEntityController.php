<?php

namespace Zenstruck\Bundle\FormBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Zenstruck\Bundle\FormBundle\Form\AjaxEntityManager;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class AjaxEntityController
{
    protected $manager;

    public function __construct(AjaxEntityManager $manager)
    {
        $this->manager = $manager;
    }

    public function findByMethodAction($entity, $method, Request $request)
    {
        $query = $request->query->get('q');

        if (!$query) {
            return new JsonResponse(array());
        }

        $results = $this->manager->findEntitiesByMethod($entity, $method, $query);

        return new JsonResponse($results);
    }

    public function findByPropertyAction($entity, $property, Request $request)
    {
        $query = $request->query->get('q');

        if (!$query) {
            return new JsonResponse(array());
        }

        $results = $this->manager->findEntitiesByProperty($entity, $property, $query);

        return new JsonResponse($results);
    }
}
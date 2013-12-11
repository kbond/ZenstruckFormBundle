<?php

namespace Zenstruck\Bundle\FormBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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

    public function findAction(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            throw new NotFoundHttpException('Must be ajax request');
        }

        $property = $request->request->get('property');
        $method = $request->request->get('method');
        $entity = $request->request->get('entity');
        $query = $request->request->get('q');

        $results = array();

        if ($query) {
            if ($property) {
                $results = $this->manager->findEntitiesByProperty($entity, $property, $query);
            } elseif ($method) {
                $results = $this->manager->findEntitiesByMethod($entity, $method, $query);
            }
        }

        return new JsonResponse($results);
    }
}

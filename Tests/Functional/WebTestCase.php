<?php

namespace Zenstruck\Bundle\FormBundle\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseWebTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Zenstruck\Bundle\FormBundle\Tests\Fixtures\App\FormTestBundle\Entity\Author;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class WebTestCase extends BaseWebTestCase
{
    /** @var \Doctrine\ORM\EntityManager */
    protected $em;

    protected function prepareEnvironment($environment = 'default')
    {
        $client = parent::createClient(array('environment' => $environment));

        $application = new Application($client->getKernel());
        $application->setAutoExit(false);
        $this->runConsole($application, "doctrine:database:drop", array("--force" => true));
        $this->runConsole($application, "doctrine:database:create");
        $this->runConsole($application, "doctrine:schema:create");

        $this->em = $client->getContainer()->get('doctrine')->getEntityManager();
        $this->addTestData();

        return $client;
    }

    protected function runConsole(Application $application, $command, array $options = array())
    {
        $options["-e"] = "test";
        $options["-q"] = null;
        $options = array_merge($options, array('command' => $command));

        return $application->run(new \Symfony\Component\Console\Input\ArrayInput($options));
    }

    protected function addTestData()
    {
        // empty db
        $this->em->createQuery('DELETE FormTestBundle:Author')
            ->execute()
        ;

        $entities[0] = new Author();
        $entities[0]->setName('Kevin');
        $entities[1] = new Author();
        $entities[1]->setName('James');

        foreach ($entities as $entity) {
            $this->em->persist($entity);
        }

        $this->em->flush();
    }
}
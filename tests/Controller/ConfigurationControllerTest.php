<?php

namespace App\Test\Controller;

use App\Entity\Configuration;
use App\Repository\ConfigurationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ConfigurationControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private ConfigurationRepository $repository;
    private string $path = '/configuration/';
    private EntityManagerInterface $manager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(Configuration::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Configuration index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $originalNumObjectsInRepository = count($this->repository->findAll());

        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'configuration[param_name]' => 'Testing',
            'configuration[param_value]' => 'Testing',
            'configuration[createdAt]' => 'Testing',
            'configuration[updatedAt]' => 'Testing',
        ]);

        self::assertResponseRedirects('/configuration/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Configuration();
        $fixture->setParam_name('My Title');
        $fixture->setParam_value('My Title');
        $fixture->setCreatedAt('My Title');
        $fixture->setUpdatedAt('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Configuration');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Configuration();
        $fixture->setParam_name('My Title');
        $fixture->setParam_value('My Title');
        $fixture->setCreatedAt('My Title');
        $fixture->setUpdatedAt('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'configuration[param_name]' => 'Something New',
            'configuration[param_value]' => 'Something New',
            'configuration[createdAt]' => 'Something New',
            'configuration[updatedAt]' => 'Something New',
        ]);

        self::assertResponseRedirects('/configuration/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getParam_name());
        self::assertSame('Something New', $fixture[0]->getParam_value());
        self::assertSame('Something New', $fixture[0]->getCreatedAt());
        self::assertSame('Something New', $fixture[0]->getUpdatedAt());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Configuration();
        $fixture->setParam_name('My Title');
        $fixture->setParam_value('My Title');
        $fixture->setCreatedAt('My Title');
        $fixture->setUpdatedAt('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/configuration/');
    }
}

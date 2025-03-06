<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class UserControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $userRepository;
    private string $path = '/user/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->userRepository = $this->manager->getRepository(User::class);

        foreach ($this->userRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('User index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'user[Nom]' => 'Testing',
            'user[date_de_naissance]' => 'Testing',
            'user[email]' => 'Testing',
            'user[role]' => 'Testing',
            'user[mot_de_passe]' => 'Testing',
            'user[photo]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->userRepository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new User();
        $fixture->setNom('My Title');
        $fixture->setDate_de_naissance('My Title');
        $fixture->setEmail('My Title');
        $fixture->setRole('My Title');
        $fixture->setMot_de_passe('My Title');
        $fixture->setPhoto('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('User');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new User();
        $fixture->setNom('Value');
        $fixture->setDate_de_naissance('Value');
        $fixture->setEmail('Value');
        $fixture->setRole('Value');
        $fixture->setMot_de_passe('Value');
        $fixture->setPhoto('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'user[Nom]' => 'Something New',
            'user[date_de_naissance]' => 'Something New',
            'user[email]' => 'Something New',
            'user[role]' => 'Something New',
            'user[mot_de_passe]' => 'Something New',
            'user[photo]' => 'Something New',
        ]);

        self::assertResponseRedirects('/user/');

        $fixture = $this->userRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getNom());
        self::assertSame('Something New', $fixture[0]->getDate_de_naissance());
        self::assertSame('Something New', $fixture[0]->getEmail());
        self::assertSame('Something New', $fixture[0]->getRole());
        self::assertSame('Something New', $fixture[0]->getMot_de_passe());
        self::assertSame('Something New', $fixture[0]->getPhoto());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new User();
        $fixture->setNom('Value');
        $fixture->setDate_de_naissance('Value');
        $fixture->setEmail('Value');
        $fixture->setRole('Value');
        $fixture->setMot_de_passe('Value');
        $fixture->setPhoto('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/user/');
        self::assertSame(0, $this->userRepository->count([]));
    }
}

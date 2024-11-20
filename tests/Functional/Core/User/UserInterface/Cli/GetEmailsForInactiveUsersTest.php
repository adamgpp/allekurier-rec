<?php

declare(strict_types=1);

namespace App\Tests\Functional\Core\User\UserInterface\Cli;

use App\Core\Common\Domain\ValueObject\Email;
use App\Core\User\Domain\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Uid\Ulid;

final class GetEmailsForInactiveUsersTest extends KernelTestCase
{
    private CommandTester $commandTester;

    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        self::bootKernel();
        $application = new Application(self::$kernel);

        $command = $application->find('app:user:get-email-for-inactive-users');
        $this->commandTester = new CommandTester($command);

        $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);
    }

    public function testShouldGetEmailsForInactiveUsers(): void
    {
        $inactiveUser1 = new User(new Ulid(), new Email('inactive-user1@example.com'));
        $this->entityManager->persist($inactiveUser1);
        $activeUser2 = new User(new Ulid(), new Email('inactive-user2@example.com'));
        $activeUser2->activate();
        $this->entityManager->persist($activeUser2);
        $inactiveUser3 = new User(new Ulid(), new Email('inactive-user3@example.com'));
        $this->entityManager->persist($inactiveUser3);
        $this->entityManager->flush();

        $this->commandTester->execute([]);

        $output = $this->commandTester->getDisplay();

        self::assertStringContainsString($inactiveUser1->getEmail()->value, $output);
        self::assertStringContainsString($inactiveUser3->getEmail()->value, $output);
        self::assertStringNotContainsString($activeUser2->getEmail()->value, $output);
    }
}

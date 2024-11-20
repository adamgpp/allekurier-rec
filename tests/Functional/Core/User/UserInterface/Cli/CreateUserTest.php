<?php

declare(strict_types=1);

namespace App\Tests\Functional\Core\User\UserInterface\Cli;

use App\Core\Common\Domain\ValueObject\Email;
use App\Core\User\Domain\User;
use App\Tests\Helpers\UlidExtractorTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Uid\Ulid;

final class CreateUserTest extends KernelTestCase
{
    use UlidExtractorTrait;

    private CommandTester $commandTester;

    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        self::bootKernel();
        $application = new Application(self::$kernel);

        $command = $application->find('app:user:create');
        $this->commandTester = new CommandTester($command);

        $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);
    }

    public function testShouldProperlySaveUser(): void
    {
        $this->commandTester->execute([
            'email' => 'user@test.email',
        ]);

        $this->commandTester->assertCommandIsSuccessful();

        $outputMessage = $this->commandTester->getDisplay();
        $newUserId = $this->extractRfc4122($outputMessage);

        self::assertStringContainsString("A new user has been created. ID: $newUserId.", $outputMessage);

        $newUser = $this->entityManager->find(User::class, $newUserId);

        self::assertInstanceOf(User::class, $newUser);
        self::assertSame('user@test.email', $newUser->getEmail()->value);
        self::assertFalse($newUser->isActive());
    }

    public function testShouldFailWhenInvalidEmailGiven(): void
    {
        $this->commandTester->execute([
            'email' => 'some-invalid.email.com',
        ]);

        self::assertSame(Command::FAILURE, $this->commandTester->getStatusCode());
        self::assertStringContainsString(
            'An email value should be a valid email address.',
            $this->commandTester->getDisplay(),
        );
    }

    public function testShouldFailWhenUserWithGivenEmailAlreadyExists(): void
    {
        $userEmail = new Email('some-valid-and-existent-test@email.com');

        $this->entityManager->persist(new User(new Ulid(), $userEmail));
        $this->entityManager->flush();

        $this->commandTester->execute([
            'email' => $userEmail->value,
        ]);

        self::assertSame(Command::FAILURE, $this->commandTester->getStatusCode());
        self::assertStringContainsString(
            "User with email `{$userEmail->value}` already exists.",
            $this->commandTester->getDisplay(),
        );
    }
}

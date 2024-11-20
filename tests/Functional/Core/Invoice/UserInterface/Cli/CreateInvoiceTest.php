<?php

declare(strict_types=1);

namespace App\Tests\Functional\Core\Invoice\UserInterface\Cli;

use App\Core\Common\Domain\ValueObject\Email;
use App\Core\Invoice\Domain\Invoice;
use App\Core\User\Domain\User;
use App\Tests\Helpers\UlidExtractorTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Uid\Ulid;

final class CreateInvoiceTest extends KernelTestCase
{
    use UlidExtractorTrait;

    private CommandTester $commandTester;

    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        self::bootKernel();
        $application = new Application(self::$kernel);

        $command = $application->find('app:invoice:create');
        $this->commandTester = new CommandTester($command);

        $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);
    }

    public function testShouldProperlySaveInvoice(): void
    {
        $user = new User(new Ulid(), new Email('user@test.email'));
        $user->activate();

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->commandTester->execute([
            'email' => $user->getEmail()->value,
            'amount' => 12345,
        ]);

        $this->commandTester->assertCommandIsSuccessful();

        $outputMessage = $this->commandTester->getDisplay();

        $newInvoiceId = $this->extractRfc4122($outputMessage);

        self::assertStringContainsString("A new invoice has been created. ID: $newInvoiceId.", $outputMessage);

        $newInvoice = $this->entityManager->find(Invoice::class, $newInvoiceId);

        self::assertInstanceOf(Invoice::class, $newInvoice);
        self::assertSame($user->getId()->toRfc4122(), $newInvoice->getUser()->getId()->toRfc4122());
        self::assertTrue($newInvoice->getUser()->isActive());
        self::assertSame(12345, $newInvoice->getAmount()->value);
    }

    public function testShouldFailWhenInvalidAmountGiven(): void
    {
        $this->commandTester->execute([
            'email' => 'some-valid-test@email.com',
            'amount' => 0,
        ]);

        self::assertSame(Command::FAILURE, $this->commandTester->getStatusCode());
        self::assertStringContainsString(
            'An amount value should be greater than 0.',
            $this->commandTester->getDisplay(),
        );
    }

    public function testShouldFailWhenInvalidEmailGiven(): void
    {
        $this->commandTester->execute([
            'email' => 'some-invalid.email.com',
            'amount' => 4321,
        ]);

        self::assertSame(Command::FAILURE, $this->commandTester->getStatusCode());
        self::assertStringContainsString(
            'An email value should be a valid email address.',
            $this->commandTester->getDisplay(),
        );
    }

    public function testShouldFailWhenThereIsNoUserWithGivenEmail(): void
    {
        $this->commandTester->execute([
            'email' => 'some-valid-but-nonexistent-test@email.com',
            'amount' => 4321,
        ]);

        self::assertSame(Command::FAILURE, $this->commandTester->getStatusCode());
        self::assertStringContainsString(
            'User with email `some-valid-but-nonexistent-test@email.com` does not exist.',
            $this->commandTester->getDisplay(),
        );
    }

    public function testShouldFailWhenUserWithGivenEmailIsNotActive(): void
    {
        $user = new User(new Ulid(), new Email('some@user.email'));
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->commandTester->execute([
            'email' => $user->getEmail()->value,
            'amount' => 4321,
        ]);

        self::assertSame(Command::FAILURE, $this->commandTester->getStatusCode());
        self::assertStringContainsString(
            sprintf('User with ID `%s` is not active.', $user->getId()->toRfc4122()),
            $this->commandTester->getDisplay(),
        );
    }
}

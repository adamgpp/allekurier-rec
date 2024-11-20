<?php

declare(strict_types=1);

namespace App\Tests\Functional\Core\Invoice\UserInterface\Cli;

use App\Core\Common\Domain\ValueObject\Email;
use App\Core\Invoice\Domain\Invoice;
use App\Core\Invoice\Domain\Status\InvoiceStatus;
use App\Core\Invoice\Domain\ValueObject\Amount;
use App\Core\User\Domain\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Uid\Ulid;

final class GetInvoicesTest extends KernelTestCase
{
    private CommandTester $commandTester;

    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        self::bootKernel();
        $application = new Application(self::$kernel);

        $command = $application->find('app:invoice:get-by-status-and-amount');
        $this->commandTester = new CommandTester($command);

        $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);
    }

    public function testShouldGetProperInvoices(): void
    {
        $user = new User(new Ulid(), new Email('test@email.com'));
        $this->entityManager->persist($user);

        $invoiceWithValidStatusAndAmount = new Invoice(new Ulid(), $user, new Amount(1001));
        $invoiceWithValidStatusAndAmount->cancel();
        $this->entityManager->persist($invoiceWithValidStatusAndAmount);

        $invoiceWithValidStatusButTooLowAmount = new Invoice(new Ulid(), $user, new Amount(1000));
        $invoiceWithValidStatusButTooLowAmount->cancel();
        $this->entityManager->persist($invoiceWithValidStatusButTooLowAmount);

        $invoiceWithValidAmountButInvalidStatus = new Invoice(new Ulid(), $user, new Amount(1001));
        $this->entityManager->persist($invoiceWithValidAmountButInvalidStatus);

        $otherInvoiceWithValidStatusAndAmount = new Invoice(new Ulid(), $user, new Amount(1234567));
        $otherInvoiceWithValidStatusAndAmount->cancel();
        $this->entityManager->persist($otherInvoiceWithValidStatusAndAmount);

        $this->entityManager->flush();

        $this->commandTester->execute(['status' => InvoiceStatus::CANCELED->value, 'amount' => 1000]);

        $output = $this->commandTester->getDisplay();

        self::assertStringContainsString($invoiceWithValidStatusAndAmount->getId()->toRfc4122(), $output);
        self::assertStringContainsString($otherInvoiceWithValidStatusAndAmount->getId()->toRfc4122(), $output);
        self::assertStringNotContainsString($invoiceWithValidStatusButTooLowAmount->getId()->toRfc4122(), $output);
        self::assertStringNotContainsString($invoiceWithValidAmountButInvalidStatus->getId()->toRfc4122(), $output);
    }
}

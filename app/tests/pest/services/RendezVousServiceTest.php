<?php

declare(strict_types=1);

namespace toubilib\tests;

use PHPUnit\Framework\TestCase;
use toubilib\application\dtos\CreateRdvDTO;
use toubilib\application\dtos\RendezVousDTO;
use toubilib\application\exceptions\ValidationException;
use toubilib\application\ports\spi\RendezVousRepositoryInterface;
use toubilib\application\usecases\RendezVousService;
use toubilib\application\validator\CreateRdvValidator;
use toubilib\domain\entities\RendezVous;

final class RendezVousServiceTest extends TestCase
{
    private function makeDto(): CreateRdvDTO
    {
        // Adapte les arguments au constructeur réel de ton CreateRdvDTO
        return new CreateRdvDTO(
            idMedecin: 'medecin-1',
            idPatient: 'patient-1',
            dateHeure: new \DateTimeImmutable('2026-10-20 10:00:00'),
            motif: 'Consultation de contrôle',
        );
    }

    public function testCreateRdvEnregistreEtRetourneUnDto(): void
    {
        $validator = $this->createMock(CreateRdvValidator::class);
        $validator->expects($this->once())->method('validate');

        $repository = $this->createMock(RendezVousRepositoryInterface::class);
        $repository->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(RendezVous::class));

        $service = new RendezVousService($validator, $repository);

        $result = $service->createRdv($this->makeDto());

        $this->assertInstanceOf(RendezVousDTO::class, $result);
    }

    public function testCreateRdvNeSauvegardePasSiValidationEchoue(): void
    {
        $validator = $this->createMock(CreateRdvValidator::class);
        $validator->method('validate')
            ->willThrowException(new ValidationException('invalide'));

        $repository = $this->createMock(RendezVousRepositoryInterface::class);
        $repository->expects($this->never())->method('save');

        $service = new RendezVousService($validator, $repository);

        $this->expectException(\DomainException::class);
        $service->createRdv($this->makeDto());
    }
}

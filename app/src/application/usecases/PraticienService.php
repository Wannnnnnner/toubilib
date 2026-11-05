<?php

declare(strict_types=1);

namespace toubilib\application\usecases;

use toubilib\application\dtos\PraticienDTO;
use toubilib\application\ports\api\PraticienServiceInterface;
use toubilib\application\ports\spi\PraticienRepositoryInterface;

class PraticienService implements PraticienServiceInterface
{
    public function __construct(private PraticienRepositoryInterface $praticienRepository)
    {
    }

    public function listerPraticiens(): array
    {
        return array_map(
            fn($p) => new PraticienDTO(
                $p->getId(),
                $p->getTitre(),
                $p->getNom(),
                $p->getPrenom(),
                $p->getVille(),
                $p->getEmail(),
                $p->getTelephone(),
                $p->accepteNouveauPatient()
            ),
            $this->praticienRepository->findAll()
        );
    }
}
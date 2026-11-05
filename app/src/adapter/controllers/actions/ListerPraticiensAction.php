<?php

declare(strict_types=1);

namespace toubilib\adapter\controllers\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use toubilib\application\ports\api\PraticienServiceInterface;

class ListerPraticiensAction
{
    public function __construct(private PraticienServiceInterface $praticienService)
    {
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $praticiens = array_map(fn($p) => [
            'id' => $p->id,
            'titre' => $p->titre,
            'nom' => $p->nom,
            'prenom' => $p->prenom,
            'ville' => $p->ville,
            'email' => $p->email,
            'telephone' => $p->telephone,
            'accepte_nouveau_patient' => $p->accepteNouveauPatient,
            'links' => [
                'self' => ['href' => '/praticiens/' . $p->id],
            ],
        ], $this->praticienService->listerPraticiens());

        $data = [
            'type' => 'collection',
            'count' => count($praticiens),
            'praticiens' => $praticiens,
            'links' => [
                'self' => ['href' => '/praticiens'],
            ],
        ];

        $response->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
}
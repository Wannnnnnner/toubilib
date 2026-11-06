<?php

declare(strict_types=1);

namespace toubilib\adapter\controllers\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpNotFoundException;
use toubilib\application\ports\api\RendezVousServiceInterface;
use toubilib\domain\exceptions\RendezVousNotFoundException;

class GetRDVByIdAction extends AbstractAction
{
    public function __construct(private RendezVousServiceInterface $serviceRendezVous)
    {
    }

    public function __invoke(Request $rq, Response $rs, array $args): Response
    {
        try {
            $rdv = $this->serviceRendezVous->consulterRdv($args['id']);
        } catch (RendezVousNotFoundException $e) {
            throw new HttpNotFoundException($rq, $e->getMessage());
        }

        $id = $rdv->id;

        $links = [
            'self' => ['href' => "/rdvs/$id/"],
            'praticien' => ['href' => "/praticiens/{$rdv->praticienId}"],
        ];

        if ($rdv->status === 'PREVU') {
            $links['annuler'] = ['href' => "/rdvs/$id/annuler"];
            $links['honorer'] = ['href' => "/rdvs/$id/honorer"];
            $links['ignorer'] = ['href' => "/rdvs/$id/ignorer"];
        }

        $data = [
            'type' => 'ressource',
            'rendez_vous' => $rdv->toArray(),
            'links' => $links,
        ];

        $rs->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE));

        return $rs
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
}
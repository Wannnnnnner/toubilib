<?php
declare(strict_types=1);

namespace toubilib\api\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use toubilib\application\dtos\CreateRdvDTO;
use toubilib\application\exceptions\ValidationException;
use toubilib\application\ports\api\RendezVousServiceInterface;
use toubilib\application\exceptions\RessourceIntrouvableException;
use toubilib\application\exceptions\RegleMetierException;

class CreerRDVAction
{
    public function __construct(private RendezVousServiceInterface $serviceRendezVous)
    {
    }

    public function __invoke(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        if (!is_array($data)) {
            return $this->erreur($response, 400, 'Bad Request', 'Corps de requête JSON absent ou invalide');
        }

        try {
            $dto = CreateRdvDTO::fromArray($data);
        } catch (ValidationException $e) {
            return $this->erreur($response, 400, 'Bad Request', $e->getMessage());
        }

        try {
            $rdv = $this->serviceRendezVous->createRdv($dto);
        } catch (ValidationException $e) {
            return $this->erreur($response, 404, 'Not Found', $e->getMessage());
        } catch (RegleMetierException $e) {
            return $this->erreur($response, 422, 'Unprocessable Entity', $e->getMessage());
        }

        $body = [
            'type' => 'ressource',
            'rendez_vous' => $rdv,
            'links' => [
                'self' => ['href' => "/rdvs/{$rdv->id}"],
                'praticien' => ['href' => "/praticiens/{$dto->idMedecin}"],
                'patient' => ['href' => "/patients/{$dto->idPatient}"],
                'annuler' => ['href' => "/rdvs/{$rdv->id}/annuler"],
            ],
        ];

        $response->getBody()->write(json_encode($body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        return $response
            ->withStatus(201)
            ->withHeader('Location', "/rdvs/{$rdv->id}")
            ->withHeader('Content-Type', 'application/json');
    }

    private function erreur(Response $response, int $status, string $title, string $detail): Response
    {
        $response->getBody()->write(json_encode([
            'type' => 'about:blank',
            'status' => $status,
            'title' => $title,
            'detail' => $detail,
        ], JSON_UNESCAPED_UNICODE));
        return $response->withStatus($status)->withHeader('Content-Type', 'application/json');
    }
}
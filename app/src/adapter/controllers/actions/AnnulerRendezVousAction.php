<?php
namespace toubilib\adapter\controllers\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpNotFoundException;
use toubilib\application\ports\api\RendezVousServiceInterface;

class AnnulerRendezVousAction extends AbstractAction
{

    public function __construct(private RendezVousServiceInterface $service)
    {
    }

    public function __invoke(Request $rq, Response $rs, array $args): Response
    {
        try {
            $dto = $this->service->annulerRdv($args['id']);
        } catch (\RuntimeException $e) {
            throw new HttpNotFoundException($rq, $e->getMessage());
        }
        $rs->getBody()->write(json_encode($dto));
        return $rs->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
}
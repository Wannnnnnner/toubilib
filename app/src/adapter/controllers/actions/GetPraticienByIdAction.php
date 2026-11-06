<?php
namespace toubilib\adapter\controllers\actions;

use Psr\Http\Message\{ResponseInterface as Response, ServerRequestInterface as Request};
use Slim\Exception\HttpNotFoundException;
use toubilib\application\ports\api\PraticienServiceInterface;
use toubilib\domain\exceptions\PraticienNotFoundException;

class GetPraticienByIdAction
{
    public function __construct(private PraticienServiceInterface $servicePraticien)
    {
    }

    public function __invoke(Request $rq, Response $rs, array $args): Response
    {
        $id = $args['id'];
        try {
            $p = $this->servicePraticien->getPraticien($id);
        } catch (PraticienNotFoundException $e) {
            throw new HttpNotFoundException($rq, $e->getMessage());
        }

        $data = [
            'type' => 'ressource',
            'praticien' => (array) $p,
            'links' => [
                'self' => ['href' => "/praticiens/$id"],
                'agenda' => ['href' => "/praticiens/$id/rdvs"],
                'praticiens' => ['href' => '/praticiens'],
            ],
        ];

        $rs->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        return $rs->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
}
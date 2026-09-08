<?php
namespace toubilib\adapters\controllers\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;

use jira\domain\exceptions\UserStoryNotFoundException;
use jira\application\ports\api\UserStoryServiceInterface;

class GetUserStoryByIdAction extends AbstractAction {

    private UserStoryServiceInterface $userStoryService;

    public function __construct(UserStoryServiceInterface $userStoryService) {
        $this->userStoryService = $userStoryService;
    }

    public function __invoke(ServerRequestInterface $rq, ResponseInterface $rs, array $args): ResponseInterface {
        $id = $args['id'];
        try {
            $userStoryDTO = $this->userStoryService->getUserStoryById($id);
        } catch (UserStoryNotFoundException $e) {
            throw new HttpNotFoundException($rq, "No user story with id $id");
        } 
        return $this->json($rs, $userStoryDTO->toArray());
    }
}


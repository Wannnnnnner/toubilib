<?php
namespace jira\adapters\controllers\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;

use jira\domain\exceptions\UserStoryNotFoundException;
use jira\application\dtos\UpdateUserStoryDTO;
use jira\application\exceptions\ValidationException;
use jira\application\ports\api\UserStoryServiceInterface;

class PatchUserStoryAction extends AbstractAction {

    private UserStoryServiceInterface $userStoryService;

    public function __construct(UserStoryServiceInterface $userStoryService) {
        $this->userStoryService = $userStoryService;
    }

    public function __invoke(ServerRequestInterface $rq, ResponseInterface $rs, array $args): ResponseInterface {
        $id = $args['id'];
        try {
            $dto = UpdateUserStoryDTO::fromArray((array) $rq->getParsedBody());
        } catch (ValidationException $e) {
            return $this->json($rs, ['errors' => $e->getErrors()], 422);
        }

        try {
            $userStoryDTO = $this->userStoryService->updateUserStory(id: $id, dto: $dto);
        } catch (UserStoryNotFoundException $e) {
            throw new HttpNotFoundException($rq, "No user story with id $id");
        }
        return $this->json($rs, $userStoryDTO->toArray());
    }
}
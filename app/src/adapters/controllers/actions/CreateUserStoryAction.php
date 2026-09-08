<?php
namespace jira\adapters\controllers\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpInternalServerErrorException;

use jira\domain\exceptions\PersistenceException;
use jira\application\exceptions\ValidationException;
use jira\application\ports\api\UserStoryServiceInterface;
use jira\application\dtos\CreateUserStoryDTO;
use jira\application\validators\CreateUserStoryValidator;

class CreateUserStoryAction extends AbstractAction {

    private UserStoryServiceInterface $userStoryService;

    public function __construct(UserStoryServiceInterface  $userStoryService) {
        $this->userStoryService = $userStoryService;
    }

    public function __invoke(ServerRequestInterface $rq, ResponseInterface $rs, array $args): ResponseInterface {
        try {
            $dto = CreateUserStoryDTO::fromArray($rq->getParsedBody() ?? []);
        } catch (ValidationException $e) {
            return $this->json($rs, ['errors' => $e->getErrors()], 422);
        }

        try {
            $userStoryDTO = $this->userStoryService->createUserStory(dto: $dto);
        } catch (\DomainException $e) {
            return $this->json($rs, ['errors' => ['ownerId' => $e->getMessage()]], 422);
        } catch (PersistenceException $e) {
            throw new HttpInternalServerErrorException($rq, "Error while persisting user story");
        }

        return $this->json($rs, $userStoryDTO->toArray(), 201);
    }
}
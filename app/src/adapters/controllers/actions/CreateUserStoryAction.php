<?php
namespace toubilib\adapters\controllers\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpInternalServerErrorException;

use toubilib\domain\exceptions\PersistenceException;
use toubilib\application\exceptions\ValidationException;
use toubilib\application\ports\api\UserStoryServiceInterface;
use toubilib\application\dtos\CreateUserStoryDTO;
use toubilib\application\validators\CreateUserStoryValidator;

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
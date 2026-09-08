<?php
namespace toubilib\adapters\controllers\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use jira\application\ports\api\UserStoryServiceInterface;
use jira\application\dtos\UserStoryOutputDTO;

class GetAllUserStoriesAction extends AbstractAction {
    
    private UserStoryServiceInterface $userStoryService;

    public function __construct(UserStoryServiceInterface $userStoryService) {
        $this->userStoryService = $userStoryService;
    }

    public function __invoke(ServerRequestInterface $rq, ResponseInterface $rs, array $args): ResponseInterface {
        return $this->json($rs, UserStoryOutputDTO::listToArray(
            $this->userStoryService->getAllUserStories()
        ));
    }
}

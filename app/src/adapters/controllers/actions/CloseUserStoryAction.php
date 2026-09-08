<?php
namespace jira\adapters\controllers\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use Slim\Exception\HttpBadRequestException;

use jira\domain\exceptions\UserStoryNotFoundException;
use jira\domain\exceptions\StatusChangeNotAllowedException;
use jira\application\ports\api\UserStoryServiceInterface;

class CloseUserStoryAction extends AbstractAction {

    private UserStoryServiceInterface $userStoryService;

    public function __construct(UserStoryServiceInterface $userStoryService) {
        $this->userStoryService = $userStoryService;
    }

    public function __invoke(ServerRequestInterface $rq, ResponseInterface $rs, array $args): ResponseInterface {
        $id = $args['id'];
        try {
            $this->userStoryService->close($id);
        } catch (UserStoryNotFoundException $e) {
            throw new HttpNotFoundException($rq, "No user story with id $id");
        } catch (StatusChangeNotAllowedException $e) {
            throw new HttpBadRequestException($rq, "Cannot close the user story");
        }
        return $this->empty($rs);
    }
}
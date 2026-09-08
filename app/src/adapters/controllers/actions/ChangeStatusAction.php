<?php
namespace toubilib\adapters\controllers\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;

use jira\domain\entities\UserStoryStatus;
use jira\domain\exceptions\StatusChangeNotAllowedException;
use jira\domain\exceptions\UserStoryNotFoundException;
use jira\application\ports\api\UserStoryServiceInterface;

class ChangeStatusAction extends AbstractAction {
    
    private UserStoryServiceInterface $userStoryService;

    public function __construct(UserStoryServiceInterface $userStoryService) {
        $this->userStoryService = $userStoryService;
    }

    public function __invoke(ServerRequestInterface $rq, ResponseInterface $rs, array $args): ResponseInterface {
        $id = $args['id'];
        $data = $rq->getParsedBody();
        $newStatus = $data['newStatus'] ?? null;
        if (!$newStatus) {
            throw new HttpBadRequestException($rq, "New status is required");
        }

        try {
            $status = UserStoryStatus::fromString($newStatus);
            if ($status === null) {
                throw new HttpBadRequestException($rq, "'$newStatus' is not a valid status");
            }
            $this->userStoryService->changeStatus($id, $status);
        } catch (UserStoryNotFoundException $e) {
            throw new HttpNotFoundException($rq, "No user story with id $id");
        } catch (StatusChangeNotAllowedException $e) {
            throw new HttpBadRequestException($rq, "Cannot change state to $newStatus");
        }
        //return $rs->withStatus(204);
        return $this->empty($rs);
    }
}

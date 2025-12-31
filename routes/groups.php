<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Services\GroupService;
use App\Support\JsonResponder;

return function ($app) {
    $groupService = new GroupService();

    // GET all groups
    $app->get('/api/groups', function (Request $request, Response $response) use ($groupService) {
        try {
            $page = (int) ($request->getQueryParams()['page'] ?? 1);
            $limit = (int) ($request->getQueryParams()['limit'] ?? 10);

            $groups = $groupService->getAll($page, $limit);

            return JsonResponder::success($response, $groups, 'Success', 200);
        } catch (\Exception $e) {
            return JsonResponder::error($response, 500, $e->getMessage());
        }
    });

    // GET active groups
    $app->get('/api/groups/active', function (Request $request, Response $response) use ($groupService) {
        try {
            $groups = $groupService->getActive();
            return JsonResponder::success($response, $groups, 'Success', 200);
        } catch (\Exception $e) {
            return JsonResponder::error($response, 500, $e->getMessage());
        }
    });

    // GET group by id
    $app->get('/api/groups/{id}', function (Request $request, Response $response, $args) use ($groupService) {
        try {
            $group = $groupService->getById($args['id']);
            return JsonResponder::success($response, $group, 'Success', 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return JsonResponder::error($response, 404, 'Group not found');
        } catch (\Exception $e) {
            return JsonResponder::error($response, 500, $e->getMessage());
        }
    });

    // POST create group
    $app->post('/api/groups', function (Request $request, Response $response) use ($groupService) {
        try {
            $data = $request->getParsedBody();

            if (empty($data['name'])) {
                return JsonResponder::error($response, 400, 'Name is required');
            }

            $group = $groupService->store($data);
            return JsonResponder::success($response, $group, 'Group created', 201);
        } catch (\Exception $e) {
            return JsonResponder::error($response, 500, $e->getMessage());
        }
    });

    // PUT update group
    $app->put('/api/groups/{id}', function (Request $request, Response $response, $args) use ($groupService) {
        try {
            $data = $request->getParsedBody();
            $group = $groupService->update($args['id'], $data);

            return JsonResponder::success($response, $group, 'Group updated', 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return JsonResponder::error($response, 404, 'Group not found');
        } catch (\Exception $e) {
            return JsonResponder::error($response, 500, $e->getMessage());
        }
    });

    // DELETE group
    $app->delete('/api/groups/{id}', function (Request $request, Response $response, $args) use ($groupService) {
        try {
            $groupService->delete($args['id']);
            return JsonResponder::success($response, [], 'Group deleted', 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return JsonResponder::error($response, 404, 'Group not found');
        } catch (\Exception $e) {
            return JsonResponder::error($response, 500, $e->getMessage());
        }
    });
};

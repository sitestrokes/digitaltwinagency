<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class ApiFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null): mixed
    {
        if (empty(session()->get('user_id'))) {
            return service('response')
                ->setStatusCode(401)
                ->setJSON(['data' => null, 'error' => 'Unauthorized. Please log in.']);
        }

        // Check user is active
        $userModel = new \App\Models\UserModel();
        $user = $userModel->find(session()->get('user_id'));
        if (!$user || $user['status'] !== 'active') {
            return service('response')
                ->setStatusCode(403)
                ->setJSON(['data' => null, 'error' => 'Account suspended or inactive.']);
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null): mixed
    {
        return $response->setHeader('Content-Type', 'application/json');
    }
}

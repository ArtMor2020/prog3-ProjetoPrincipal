<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Config\Services;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {

        if (strtolower($request->getMethod()) === 'options') {
            return $request;
        }
        // ------------------------------------

        $key = getenv('jwt.secret');
        $header = $request->getHeaderLine('Authorization');
        $token = null;

        if (!empty($header)) {
            if (preg_match('/Bearer\s(\S+)/', $header, $matches)) {
                $token = $matches[1];
            }
        }

        if (is_null($token)) {
            return Services::response()
                ->setJSON(['message' => 'Token de acesso não fornecido ou inválido'])
                ->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
        }

        try {
            JWT::decode($token, new Key($key, 'HS256'));
        } catch (\Exception $e) {
            return Services::response()
                ->setJSON(['message' => 'Token inválido: ' . $e->getMessage()])
                ->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
        }
        
        return $request;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
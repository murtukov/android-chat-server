<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class IndexController
 *
 * @Route("/api", name="register")
 * @package App\Controller
 */
class ApiController extends AbstractController
{
    /**
     * @Route("/login", name="login")
     * @param Request $request
     * @return JsonResponse
     */
    public function test(Request $request)
    {
        $json = json_decode($request->getContent(), true);

        return new JsonResponse([
            'username' => $json['username'],
            'password' => $json['password'],
        ]);
    }

    /**
     * @Route("/register", name="register")
     * @param Request $request
     * @param ValidatorInterface $validator
     * @return JsonResponse
     */
    public function register(Request $request, ValidatorInterface $validator)
    {
        $json = \json_decode($request->getContent(), true);

        $newUser = new User();
        $newUser->setUsername($json['username']);
        $newUser->setEmail($json['email']);
        $newUser->setPasswordPlain($json['password']);
        $newUser->setCountry($json['country']);

        $errors = $validator->validate($newUser);

        if ($errors->count() > 0) {
            return new JsonResponse([
                'category' => 'validation',
                'message' => ''
            ], Response::HTTP_BAD_REQUEST);
        }


        return new JsonResponse();
    }
}
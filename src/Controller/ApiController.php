<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Response\RequestBodyEmptyResponse;
use App\Response\ValidationErrorsResponse;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Guard\JWTTokenAuthenticator;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTManager;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use function json_decode;

/**
 * Class IndexController
 *
 * @Route("/api", name="api_")
 * @package App\Controller
 */
class ApiController extends AbstractController
{
    private $em;
    private $validator;

    public function __construct(EntityManagerInterface $em, ValidatorInterface $validator)
    {
        $this->em = $em;
        $this->validator = $validator;
    }

    /**
     * @Route("/login", name="login")
     */
    public function login(Request $request, JWTTokenManagerInterface $jwt, UserPasswordEncoderInterface $encoder): JsonResponse
    {
        $json = json_decode($request->getContent());

        $user = $this->em->getRepository(User::class)->findOneBy([
            'username' => $json->username
        ]);

        if (null !== $user && $encoder->isPasswordValid($user, $json->password)) {
            return new JsonResponse(['token' => $jwt->create($user)]);
        } else {
            return new JsonResponse(['message' => 'Invalid credentials'], Response::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * @Route("/register", name="register")
     */
    public function register(Request $request, ValidatorInterface $validator, UserPasswordEncoderInterface $encoder): JsonResponse
    {
        $json = json_decode($request->getContent(), true);

        if (count($json) === 0) {
            return new RequestBodyEmptyResponse();
        }

        $newUser = new User();
        $newUser->setUsername($json['username']);
        $newUser->setEmail($json['email']);
        $newUser->setPassword($encoder->encodePassword($newUser, $json['password']));
        $newUser->setCountry($json['country']);

        $errors = $validator->validate($newUser);

        if ($errors->count() > 0) {
            return new ValidationErrorsResponse($errors);
        }

        $this->em->persist($newUser);
        $this->em->flush();

        return new JsonResponse(['user' => $newUser->toArray()]);
    }
}
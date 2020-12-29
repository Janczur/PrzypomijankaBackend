<?php

namespace App\Modules\Security\Controller;

use App\Modules\Security\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

/** @Route("/auth", name="auth_") */
class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="login", methods={"POST"})
     */
    public function login(): JsonResponse
    {
        return $this->json([
            'message' => 'success',
            'user' => $this->getUser()
        ], 200, [], [
            'groups' => ['user']
        ]);
    }

    /**
     * @Route("/logout", name="logout")
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        return $this->json(['message' => 'success']);
    }

    /**
     * @Route("/register", name="register", methods={"POST"})
     */
    public function register(
        EntityManagerInterface $em,
        UserPasswordEncoderInterface $passwordEncoder,
        Request $request,
        ValidatorInterface $validator
    ): JsonResponse
    {
        $constraint = new Assert\Collection([
            'name' => new Assert\Length(['min' => 3, 'max' => 255]),
            'email' => new Assert\Email(),
            'password' => new Assert\Length(['min' => 6]),
        ]);
        $violations = $validator->validate($request->request->all(), $constraint);
        if (0 !== count($violations)){
            return $this->json([
                'message' => 'error',
                'errors' => $violations
            ], 400);
        }
        $user = new User();
        $user->setName($request->request->get("name"));
        $user->setEmail($request->request->get("email"));
        $encodedPassword = $passwordEncoder->encodePassword($user, $request->request->get("password"));
        $user->setPassword($encodedPassword);

        $em->persist($user);
        $em->flush();
        return $this->json([
            'message' => 'success',
            'user' => $user
        ], 200, [], [
            'groups' => ['user']
        ]);
    }
}

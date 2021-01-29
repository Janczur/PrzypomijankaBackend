<?php


namespace App\Modules\Security\Controller;


use App\Modules\Security\Entity\User;
use App\Modules\Security\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/users", name="users_")
 * @IsGranted("ROLE_ADMIN")
 */
class UserController extends AbstractController
{
    private UserRepository $userRepository;

    private SerializerInterface $serializer;

    public function __construct(UserRepository $userRepository, SerializerInterface $serializer)
    {
        $this->userRepository = $userRepository;
        $this->serializer = $serializer;
    }

    /**
     * @Route("/", name="index")
     */
    public function index(): JsonResponse
    {
        $users = $this->userRepository->findAll();
        $normalizedUsers = $this->serializer->normalize($users, 'json', ['groups' => 'user:read']);
        return $this->json($normalizedUsers);
    }

    /**
     * @Route("/{id}", name="show", requirements={"id"="\d+"})
     * @return JsonResponse
     */
    public function show(User $user): JsonResponse
    {
        $normalizedUser = $this->serializer->normalize($user, 'json', ['groups' => 'user:read']);
        return $this->json($normalizedUser);
    }
    /**
     * @Route("/{id}/delete", name="delete", requirements={"id"="\d+"}, methods={"DELETE"})
     * @return JsonResponse
     */
    public function delete(User $user, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($user);
        $em->flush();
        return $this->json(['message' => 'success']);
    }

}
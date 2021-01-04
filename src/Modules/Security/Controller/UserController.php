<?php


namespace App\Modules\Security\Controller;


use App\Modules\Security\Entity\User;
use App\Modules\Security\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/users", name="users_")
 * @IsGranted("ROLE_ADMIN")
 */
class UserController extends AbstractController
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @Route("/", name="index")
     */
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'success',
            'users' => $this->userRepository->findAll()
        ], 200, [], [
            'groups' => ['user']
        ]);
    }

    /**
     * @Route("/{id}", name="show", requirements={"id"="\d+"})
     * @return JsonResponse
     */
    public function show(User $user): JsonResponse
    {
        return $this->json([
            'message' => 'success',
            'user' => $user
        ], 200, [], [
            'groups' => ['user']
        ]);
    }
    /**
     * @Route("/{id}/delete", name="delete", requirements={"id"="\d+"}, methods={"DELETE"})
     * @return JsonResponse
     */
    public function delete(User $user): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();
        return $this->json(['message' => 'success']);
    }

}
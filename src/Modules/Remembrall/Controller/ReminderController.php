<?php

namespace App\Modules\Remembrall\Controller;

use App\Modules\Remembrall\Entity\CyclicType;
use App\Modules\Remembrall\Entity\Reminder;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/reminders", name="reminders_")
 * @IsGranted("ROLE_USER")
 */
class ReminderController extends AbstractController
{
    private SerializerInterface $serializer;

    private ValidatorInterface $validator;

    /**
     * @param SerializerInterface $serializer
     * @param ValidatorInterface $validator
     */
    public function __construct(SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    /**
     * @Route("/", name="index")
     */
    public function index(): JsonResponse
    {
        $reminders = $this->getUser()->getReminders();
        $normalizedReminders = $this->serializer->normalize($reminders, 'json', ['groups' => 'reminder:read']);
        return $this->json($normalizedReminders);
    }

    /**
     * @Route("/{id}", name="show", requirements={"id"="\d+"}, methods={"GET"})
     */
    public function show(Reminder $reminder): JsonResponse
    {
        $this->denyAccessUnlessGranted('view', $reminder);
        $normalizedReminder = $this->serializer->normalize($reminder, 'json', ['groups' => 'reminder:read']);
        return $this->json($normalizedReminder);
    }

    /**
     * @Route("/save", name="save", methods={"POST"})
     * @ParamConverter("reminder", converter="fos_rest.request_body")
     */
    public function store(Reminder $reminder, EntityManagerInterface $em): JsonResponse
    {
        if ($validationErrors = $this->validateReminder($reminder)) {
            return $this->json($validationErrors, 400);
        }
        if ($reminder->isCyclic()) {
            $cyclicType = $em->getRepository(CyclicType::class)->findOneBy(
                ['name' => $reminder->getCyclic()->getType()->getName()]
            );
            $reminder->getCyclic()->setType($cyclicType);
        }
        $reminder->setUser($this->getUser());
        $em->persist($reminder);
        $em->flush();
        $normalizedReminder = $this->serializer->normalize($reminder, 'json', ['groups' => 'reminder:read']);
        return $this->json($normalizedReminder);
    }

    private function validateReminder(Reminder $reminder)
    {
        $reminderValidationErrors = $this->validator->validate($reminder);
        if (count($reminderValidationErrors) > 0) {
            return $reminderValidationErrors;
        }
        if (!$reminder->isCyclic()) {
            return false;
        }
        $cyclicValidationErrors = $this->validator->validate($reminder->getCyclic());
        if (count($cyclicValidationErrors) > 0) {
            return $cyclicValidationErrors;
        }
        $cyclicTypeValidationErrors = $this->validator->validate($reminder->getCyclic()->getType());
        if (count($cyclicTypeValidationErrors) > 0) {
            return $cyclicTypeValidationErrors;
        }
        return false;
    }

    /**
     * @Route("/{id}/update", name="update", methods={"PUT"})
     * @ParamConverter("updatedReminder", converter="fos_rest.request_body")
     */
    public function update(Reminder $reminder, Reminder $updatedReminder, EntityManagerInterface $em): JsonResponse
    {
        $this->denyAccessUnlessGranted('edit', $reminder);
        if ($validationErrors = $this->validateReminder($updatedReminder)) {
            return $this->json($validationErrors, 400);
        }
        if ($reminder->isCyclic()) {
            $cyclicType = $em->getRepository(CyclicType::class)->findOneBy(
                ['name' => $updatedReminder->getCyclic()->getType()->getName()]
            );
            $updatedReminder->getCyclic()->setType($cyclicType);
            $reminder->setCyclic($updatedReminder->getCyclic());
        }
        $reminder->setTitle($updatedReminder->getTitle())
            ->setDescription($updatedReminder->getDescription())
            ->setRemindAt($updatedReminder->getRemindAt())
            ->setPreRemindAt($updatedReminder->getPreRemindAt())
            ->setChannels($updatedReminder->getChannels())
            ->setActive($updatedReminder->getActive());
        $em->flush();
        $normalizedReminder = $this->serializer->normalize($reminder, 'json', ['groups' => 'reminder:read']);
        return $this->json($normalizedReminder);
    }

    /**
     * @Route("/{id}/delete", name="delete", requirements={"id"="\d+"}, methods={"DELETE"})
     */
    public function destroy(Reminder $reminder, EntityManagerInterface $em): JsonResponse
    {
        $this->denyAccessUnlessGranted('delete', $reminder);
        $em->remove($reminder);
        $em->flush();
        return $this->json(['message' => 'success']);
    }
}

<?php

namespace App\Controller\Api;

use App\Entity\Customer;
use App\Service\ResponseService;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/api")
 */
class ApiController extends AbstractController
{

    private EntityManagerInterface $entityManager;
    private SerializerInterface $serializer;
    private ResponseService $responseService;

    public function __construct(EntityManagerInterface $entityManager, SerializerInterface $serializer, ResponseService $responseService)
    {
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
        $this->responseService = $responseService;
    }

    /**
     * @Route("/test", methods={"GET"})
     * @OA\Response(
     *     response=200,
     *     description="Returns the rewards of an user",
     * )
     * @OA\Tag(name="Order")
     */
    public function test(Request $request)
    {
        /** @var Customer $customer */
        $customer = $this->entityManager->getRepository(Customer::class)->find(5);
        return $this->responseService->create($customer);
    }

}

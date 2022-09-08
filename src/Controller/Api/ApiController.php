<?php

namespace App\Controller\Api;

use App\Service\OrderService;
use App\Service\ResponseService;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/order")
 */
class ApiController extends AbstractController
{

    private EntityManagerInterface $entityManager;
    private ResponseService $responseService;
    private OrderService $orderService;

    public function __construct(EntityManagerInterface $entityManager, ResponseService $responseService, OrderService $orderService)
    {
        $this->entityManager = $entityManager;
        $this->responseService = $responseService;
        $this->orderService = $orderService;
    }

    /**
     * @Route("/add", methods={"POST"})
     * @OA\Response(
     *     response=200,
     *     description="Returns the resulting order",
     * )
     * @OA\Tag(name="Order")
     */
    public function add(Request $request): Response
    {
        try {
            $response = $this->orderService->create(json_decode($request->getContent(), true));
        }catch (\Exception $e){
            return $this->responseService->create($e->getMessage());
        }

        return $this->responseService->create($response);

    }

}

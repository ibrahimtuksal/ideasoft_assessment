<?php

namespace App\Controller\Api;

use App\Entity\Order;
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
    /**
     * @Route("/add", methods={"POST"})
     * @OA\Response(
     *     response=201,
     *     description="Returns the resulting order",
     * )
     * @OA\Tag(name="Order")
     * @param Request $request
     * @param OrderService $orderService
     * @param ResponseService $responseService
     * @return Response
     */
    public function add(Request $request, OrderService $orderService, ResponseService $responseService): Response
    {
        try {
            $response = $orderService->create(json_decode($request->getContent(), true));
        }catch (\Exception $e){
            $response = $e->getMessage();
        }

        return $responseService->create($response);
    }

    /**
     * @Route("/delete/{order}", methods={"GET"})
     * @OA\Response(
     *     response=200,
     *     description="Returns information about the deleted order",
     * )
     * @OA\Tag(name="Order")
     * @param int $order
     * @param ResponseService $responseService
     * @param OrderService $orderService
     * @return Response
     */
    public function delete(int $order, ResponseService $responseService, OrderService $orderService): Response
    {
        try {
            $response = $orderService->delete($order);
        }catch (\Exception $exception){
            $response = $exception->getMessage();
        }
        return $responseService->create($response);
    }

    /**
     * @Route("/list/{order}", methods={"GET"})
     * @OA\Response(
     *     response=200,
     *     description="Returns the selected order",
     * )
     * @OA\Tag(name="Order")
     * @param int $order
     * @param ResponseService $responseService
     * @param OrderService $orderService
     * @return Response
     */
    public function listing(int $order, ResponseService $responseService, OrderService $orderService): Response
    {
        return $responseService->create($orderService->list($order));
    }
}

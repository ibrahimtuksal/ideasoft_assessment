<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

class ResponseService
{
    /**
     * @var SerializerInterface
     */
    private SerializerInterface $serializer;

    /**
     * @param SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @param $data
     * @param bool $json
     * @return Response
     */
    public function create($data, bool $json = false): Response
    {
        if ($json){
            $data = json_decode($data, true);
        }

        $response = new Response($this->serialize($data));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @param mixed $data
     * @return string
     */
    private function serialize($data): string
    {
        return $this->serializer->serialize($data, 'json', ['groups' => ['normal']]);
    }

}
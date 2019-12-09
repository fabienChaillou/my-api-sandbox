<?php

namespace App\Controller;

use App\Entity\Player;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AddPlayerController extends AbstractController
{
    /**
     * for test
     * Request: POST http://localhost:80/add/player  body {"username": "foo"}
     * Response: code 200
     * {
     * "id": 52,
     * "username": "foo",
     * "createdAt": "2019-12-09T17:29:14+00:00",
     * "updatedAt": "2019-12-09T17:29:14+00:00",
     * "creator": null,
     * "games": [],
     * "avatar": null
     * }
     *
     * @Route("/add/player", name="add_player", methods={"POST"})
     */
    public function index(Request $request, SerializerInterface $serializer, ValidatorInterface $validator): JsonResponse
    {
        $player = $serializer->deserialize($request->getContent(), Player::class, 'json');

        if (count($errors = $validator->validate($player))) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST, ['X-Frame-Options' => 'deny']);
        }

        $manager = $this->getDoctrine()->getManager();
        $manager->persist($player);
        $manager->flush();

        // TODO: Send a SMS here

        return $this->json($player, Response::HTTP_OK, ['X-Frame-Options' => 'deny']);
    }
}

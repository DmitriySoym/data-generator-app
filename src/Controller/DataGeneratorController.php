<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Faker\Factory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class DataGeneratorController extends AbstractController
{
    #[Route('/data/generator', name: 'app_data_generator')]
    public function index(Request $request): JsonResponse
    {

        $content = json_decode($request->getContent(), true);

        $language = $content['language'] ?? 'en_US';
        $page = $content['page'] ?? 1;
        $limit = $content['limit'] ?? 20;
        $userSeed = $content['seed'] ?? 0;

        $faker = Factory::create($language);

        // $compositeSeed = intval($userSeed) + $page;

        $data = [];
        for ($i = 0; $i < $limit; $i++) {
            $data[] = [
                'number' => ($page - 1) * $limit + $i + 1,
                'ID' => $faker->uuid,
                'name' => $faker->name,
                'adress' => $faker->address,
                'phone' => $faker->phoneNumber
            ];
        }

        return $this->json($data);
    }
}

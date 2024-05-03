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
        $faker = Factory::create();

        //========================================================
            $limit = 10;
        //========================================================
        $data = [];
        for ($i = 0; $i < $limit; $i++) {
            $data[] = [
                'number' => $i + 1,
                'ID' => $faker->uuid,
                'name' => $faker->name,
                'adress' => $faker->address,
                'phone' => $faker->phoneNumber
            ];
        }

        return $this->json($data);
    }
}

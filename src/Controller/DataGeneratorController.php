<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class DataGeneratorController extends AbstractController
{

    private $regionAlphabet = [
        'en_US' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
        'ru_RU' => 'абвгдеёжзийклмнопрстуфхцчшщъыьэюяАБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯ',
        'de_DE' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZÄäÖöÜü',
        'pl_PL' => 'abcdefghijklmnoprstuwyzABCDEFGHIJKLMNOPRSTUWYZ'
    ];
    #[Route('/data/generator', name: 'app_data_generator')]
    public function index(Request $request): JsonResponse
    {

        $content = json_decode($request->getContent(), true);

        $language = $content['language'] ?? 'en_US';
        $limit = $content['limit'] ?? 20;
        $page = $content['page'] ?? 1;
        $userSeed = $content['seed'] ?? 0;

        $faker = Factory::create($language);

        $compositeSeed = intval($userSeed) + $page;
        $faker->seed($compositeSeed);

        $data = [];
        for ($i = 0; $i < $limit; $i++) {
            $data[] = [
                'ID' => $faker->uuid,
                'name' => $faker->name,
                'address' => $faker->address,
                'phone' => $faker->phoneNumber
            ];
        }

        return $this->json($data);
    }

        #[Route('/data/add-errors', name: 'app_data_add-errors')]
    public function addErrors(Request $request): JsonResponse
    {
        $content = json_decode($request->getContent(), true);
        $data = $content['data'] ?? [];
        $errorsPerRecord = $content['errorsPerRecord'] ?? 0;
        $language = $content['language'] ?? 'en_US';

        $faker = Factory::create($language);
        foreach ($data as &$item) {
            $item['name'] = $this->makeErrors($item['name'], $language, $errorsPerRecord, $faker);
            $item['address'] = $this->makeErrors($item['address'], $language, $errorsPerRecord, $faker);
            $item['phone'] = $this->makeErrors($item['phone'], $language, $errorsPerRecord, $faker);
        }

        return $this->json($data);
    }

    private function makeErrors($text, $language, $errorsPerRecord,  Generator $faker)
    {
        if (mb_strlen($text) < $errorsPerRecord) {
            $errorsPerRecord = mb_strlen($text);
        }

        for ($j = 0; $j < floor($errorsPerRecord); $j++) {
            $text = $this->getErrorType($text, $language, $faker);
        }

        $fractionalPart = $errorsPerRecord - floor($errorsPerRecord);
        if ($faker->boolean($fractionalPart * 100)) {
            $text = $this->getErrorType($text, $language, $faker);
        }
        return $text;
    }

    private function getErrorType($text, $language, Generator $faker)
    {
        $type = $faker->randomElement(['delete', 'add', 'swap']);
        switch ($type) {
            case 'delete':
                $pos = $faker->numberBetween(0, mb_strlen($text) - 1);
                $text = mb_substr($text, 0, $pos) . mb_substr($text, $pos + 1);
                break;
            case 'add':
                $pos = $faker->numberBetween(0, mb_strlen($text));
                $char = $this->getRandomLetter($language);
                $text = mb_substr($text, 0, $pos) . $char . mb_substr($text, $pos);
                break;
            case 'swap':
                if (mb_strlen($text) > 1) {
                    $pos = $faker->numberBetween(0, mb_strlen($text) - 1);
                    $temp1 = mb_substr($text, $pos, 1);
                    $temp2 = mb_substr($text, $pos + 1, 1);
                    $text = mb_substr($text, 0, $pos) . $temp2 . $temp1 . mb_substr($text, $pos + 2);
                }
                break;
        }
        return $text;
    }

    private function getRandomLetter($language)
    {
        $alphabet = $this->regionAlphabet[$language];
        $index = mt_rand(0, mb_strlen($alphabet) - 1);
        return mb_substr($alphabet, $index, 1);

    }

}

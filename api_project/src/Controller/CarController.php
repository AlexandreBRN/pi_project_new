<?php

namespace App\Controller;

use App\Entity\Car;
use App\Entity\Owner;
use App\Repository\CarRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/car')]
class CarController extends AbstractController
{
    #[Route('/', name: 'new_car' ,methods: ["post"])]
    public function new(EntityManagerInterface $em, Request $request)
    {
        $parameters = json_decode($request->getContent(), true);
        $ownerRepoitory = $em->getRepository(Owner::class);
        $owner = $ownerRepoitory->find($parameters["owner"]);
        if (is_null ($owner)) {
            throw $this->createNotFoundException("this owner does not exist");
        } 
        $car = new Car();
        $car->setModel($parameters["model"]);
        $car->setPlate($parameters["plate"]);
        $car->setOwner($owner);
        $em->persist($car);
        $em->flush();
        return $this->json("car saved");
    }

    #[Route('/', name: 'get_all_car')]
    public function index(CarRepository $carRepository): JsonResponse
    {
        $cars = $carRepository->findAll();  
        return $this->json($cars);
    }

    #[Route('/{id}', name: 'delete_car', methods:["DELETE"])]
    public function delete(EntityManagerInterface $em, int $id)
    {
        $carRepository = $em->getRepository(Car::class);
        $car = $carRepository->find($id);
        $em->remove($car);
        $em->flush();
        return $this->json("car removed");
    }

    #[Route('/{id}', name: 'edit_car', methods:["PUT"])]
    public function edit(EntityManagerInterface $em, int $id, Request $request): JsonResponse  
    {
        $carRepository = $em->getRepository(Car::class);
        $car = $carRepository->find($id);
        $parameters = json_decode($request->getContent(), true);
        $car->setModel($parameters["model"]);
        $car->setPlate($parameters["plate"]);
        $em->persist($car);
        $em->flush();
        return $this->json("car edit");
    }

    
}

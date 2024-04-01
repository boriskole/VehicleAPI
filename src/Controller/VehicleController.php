<?php

namespace App\Controller;

use App\Entity\Vehicle;
use App\Repository\VehicleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/api/vehicle')]
class VehicleController extends AbstractController
{

    #[Route(name: 'get_vehicles', methods: [Request::METHOD_GET])]
    public function index(VehicleRepository $vehicleRepository): Response
    {
        return $this->json($vehicleRepository->findAll());
    }

    #[Route(path: '/{id}',name: 'show_vehicle', methods: [Request::METHOD_GET])]
    public function show(int $id, VehicleRepository $vehicleRepository): Response
    {
        $foundVehicle = $vehicleRepository->find($id);

        if (!$foundVehicle) {
            return $this->json("No matching vehicle found with id: {$id}", 404);
        }

        return $this->json($foundVehicle);
    }

    #[Route(path: '/{id}', name: 'delete_vehicle', methods: [Request::METHOD_DELETE])]
    public function destroy(int $id, EntityManagerInterface $entityManager): Response
    {

        $foundVehicle = $entityManager->getRepository(Vehicle::class)->find($id);

        if (!$foundVehicle) {
            return $this->json("No matching vehicle found with id: {$id}", 404);
        }

        $entityManager->remove($foundVehicle);
        $entityManager->flush();

        return $this->json([], status: 204);

    }

    #[Route(name: 'create_vehicle', methods: [Request::METHOD_POST])]
    public function store(Request $request, EntityManagerInterface $entityManager): Response
    {
        $decodedJson = json_decode($request->getContent(), true);

        $vehicle = new Vehicle();
        $vehicle->setMake($decodedJson['make']);
        $vehicle->setModel($decodedJson['model']);
        $vehicle->setYear($decodedJson['year']);

        $entityManager->persist($vehicle);
        $entityManager->flush();

        return $this->json($vehicle);

    }

    #[Route(path: '/{id}', name: 'update_vehicle', methods: [Request::METHOD_PUT])]
    public function update(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {

        $foundVehicle = $entityManager->getRepository(Vehicle::class)->find($id);

        if (!$foundVehicle) {
            return $this->json("No matching vehicle found with id: {$id}", 404);
        }

        $decodedJson = json_decode($request->getContent(), true);

        $foundVehicle->setMake($decodedJson['make']);
        $foundVehicle->setModel($decodedJson['model']);
        $foundVehicle->setYear($decodedJson['year']);

        $entityManager->flush();

        return $this->redirectToRoute('show_vehicle', [
            'id' => $id
        ]);

    }

}
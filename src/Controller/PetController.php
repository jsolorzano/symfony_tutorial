<?php

namespace App\Controller;

use App\Repository\PetRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class PetController
 * @package App\Controller
 * 
 * @Route(path="/api/")
 */
class PetController
{
    private $petRepository;

    public function __construct(PetRepository $petRepository)
    {
        $this->petRepository = $petRepository;
    }
	
	/**
     * @Route("pet", name="add_pet", methods={"POST"})
     */
    public function add(Request $request): JsonResponse
    {
		$data = json_decode($request->getContent(), true);
		
		$name = $data['name'];
		$type = $data['type'];
		$photoUrls = $data['photoUrls'];
		
		if(empty($name) || empty($type)){
			throw new NotFoundHttpException('Expecting mandatory parameters!');
		}
		
		$this->petRepository->savePet($name, $type, $photoUrls);
		
        return new JsonResponse(['status' => 'Pet created!'], Response::HTTP_CREATED);
    }
	
	/**
     * @Route("pet/{id}", name="get_one_pet", methods={"GET"})
     */
    public function get($id): JsonResponse
    {
		$pet = $this->petRepository->findOneBy(['id' => $id]);
		
		$data = [
			'id' => $pet->getId(), 
			'name' => $pet->getName(), 
			'type' => $pet->getType(), 
			'photoUrls' => $pet->getPhotoUrls()
		];
		
        return new JsonResponse($data, Response::HTTP_OK);
    }
	
	/**
     * @Route("pets", name="get_all_pets", methods={"GET"})
     */
    public function getAll(): JsonResponse
    {
		$pets = $this->petRepository->findAll();
		
		$data = [];
		
		foreach($pets as $pet){
			$data[] = [
				'id' => $pet->getId(), 
				'name' => $pet->getName(), 
				'type' => $pet->getType(), 
				'photoUrls' => $pet->getPhotoUrls()
			];
		}
		
        return new JsonResponse($data, Response::HTTP_OK);
    }
    
    /**
     * @Route("pet/{id}", name="update_pet", methods={"PUT"})
     */
    public function update($id, Request $request): JsonResponse
    {
		$pet = $this->petRepository->findOneBy(['id' => $id]);
		$data = json_decode($request->getContent(), true);
		
		empty($data['name']) ? true : $pet->setName($data['name']);
		empty($data['type']) ? true : $pet->setType($data['type']);
		empty($data['photoUrls']) ? true : $pet->setPhotoUrls($data['photoUrls']);
		
		$updatedPet = $this->petRepository->updatePet($pet);
		
        return new JsonResponse(['status' => 'Pet updated!'], Response::HTTP_OK);
    }
    
    /**
     * @Route("pet/{id}", name="delete_pet", methods={"DELETE"})
     */
    public function delete($id): JsonResponse
    {
		$pet = $this->petRepository->findOneBy(['id' => $id]);
		
		$this->petRepository->removePet($pet);
		
        return new JsonResponse(['status' => 'Pet deleted!'], Response::HTTP_OK);
    }
}

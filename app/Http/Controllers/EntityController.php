<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\EntityRepository;

class EntityController extends Controller
{
    private $repository;

    public function __construct(EntityRepository $entityRepository)
    {
        $this->repository = $entityRepository;
    }

    public function getAllEntities(Request $request)
    {
        return $this->repository->getAllEntities(auth()->id());
    }

    public function getEntityByID($id)
    {
        return $this->repository->getEntityByID(auth()->id(), $id);
    }

    public function updateEntity(Request $request, $id)
    {        
        $data = $request->all();
        return $this->repository->updateEntity(auth()->id(), $id, $data);
    }

    public function createEntity(Request $request)
    {
        $request->validate([
            'type' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'measurement_type' => 'required|string',
            'ingredients' => 'sometimes|array',
            'ingredients.*.id' => 'required|integer|exists:entities,id',
            'ingredients.*.measurement_type' => 'required|string',
            'ingredients.*.measurement_amount' => 'required|numeric|min:0',
        ]);

        return $this->repository->createEntity($request->all(), auth()->id());    
    }

    public function deleteEntity(Request $request, $id)
    {
        return $this->repository->deleteEntity(auth()->id(), $id);    
    }

    public function getIngredients(Request $request)
    {
        return $this->repository->getIngredients(null);
    }

    public function getDishes(Request $request)
    {
        return $this->repository->getDishes(null);
    }

    public function getMixes(Request $request)
    {
        return $this->repository->getMixes(null);
    }
}

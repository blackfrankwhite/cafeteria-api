<?php

namespace App\Repositories;

use App\Models\Entity;
use App\Models\EntityMap;
use Illuminate\Support\Facades\DB;

class EntityRepository
{
    public function getWithIngredients($userId)
    {
        $entities = Entity::where('user_id', $userId)
            ->where('type', 'dish')
            ->with(['ingredients.child', 'ingredients.child.ingredients.child'])
            ->get()
            ->toArray();

        $outputArray = [];
        foreach ($entities as $entity) {
            $entityData = [
                'id' => $entity['id'],
                'title' => $entity['title'],
                'price' => $entity['price'],
                'ingredients' => []
            ];

            foreach ($entity['ingredients'] as $ingredient) {
                $ingredientData = [
                    'id' => $ingredient['child']['id'],
                    'title' => $ingredient['child']['title'],
                    'measurement_type' => $ingredient['measurement_type'],
                    'measurement_amount' => $ingredient['measurement_amount'],
                    'price' => $ingredient['child']['price'],
                    'ingredients' => []
                ];

                if ($ingredient['child']['type'] === 'mix' && !empty($ingredient['child']['ingredients'])) {
                    foreach ($ingredient['child']['ingredients'] as $subIngredient) {
                        $ingredientData['ingredients'] []= [
                            'id' => $subIngredient['child']['id'],
                            'title' => $subIngredient['child']['title'],
                            'measurement_type' => $subIngredient['measurement_type'],
                            'measurement_amount' => $subIngredient['measurement_amount'],
                            'price' => $subIngredient['child']['price']
                        ];
                    }
                }

                $entityData['ingredients'][] = $ingredientData;
            }

            $outputArray[] = $entityData;
        }

        return response()->json($outputArray);
    }

    public function calculateIngredientsNeeded($inputData)
    {
        $entityIds = array_column($inputData, 'entity_id');
        $quantities = array_column($inputData, 'quantity', 'entity_id');

        $entities = Entity::whereIn('id', $entityIds)
            ->with(['ingredients.child', 'ingredients.child.ingredients.child'])
            ->get();

        $ingredientsNeeded = [];
        $totalPrice = 0;

        foreach ($entities as $entity) {
            $entityQuantity = $quantities[$entity->id] ?? 1;

            foreach ($entity->ingredients as $ingredient) {
                $ingredientId = $ingredient->child_id;
                $ingredientTitle = $ingredient->child ? $ingredient->child->title : null;
                $measurementType = $ingredient->measurement_type;
                $measurementAmount = $ingredient->measurement_amount;
                $finalAmount = floatVal($measurementAmount * $entityQuantity);

                $finalAmount = round($finalAmount, 2);
                $ingredientPrice = $ingredient->child->price ?? 0;
                $ingredientTotalPrice = $ingredientPrice * $finalAmount;

                if (isset($ingredientsNeeded[$ingredientId])) {
                    $ingredientsNeeded[$ingredientId]['amount'] += $finalAmount;
                    $ingredientsNeeded[$ingredientId]['amount'] = round($ingredientsNeeded[$ingredientId]['amount'], 2);
                    $ingredientsNeeded[$ingredientId]['total_price'] += $ingredientTotalPrice;
                } else if ($ingredient->child->type != 'mix') {
                    $ingredientsNeeded[$ingredientId] = [
                        'id' => $ingredientId,
                        'title' => $ingredientTitle,
                        'measurement_type' => $measurementType,
                        'amount' => $finalAmount,
                        'price_per_unit' => $ingredientPrice,
                        'total_price' => $ingredientTotalPrice,
                    ];
                }

                $totalPrice += $ingredientTotalPrice;

                if ($ingredient->child && $ingredient->child->type === 'mix') {
                    foreach ($ingredient->child->ingredients as $subIngredient) {
                        $subIngredientId = $subIngredient->child_id;
                        $subIngredientTitle = $subIngredient->child ? $subIngredient->child->title : null;
                        $subMeasurementType = $subIngredient->measurement_type;
                        $subMeasurementAmount = $subIngredient->measurement_amount * $entityQuantity;
                        $subIngredientPrice = $subIngredient->child->price ?? 0;
                        $subIngredientTotalPrice = $subIngredientPrice * $subMeasurementAmount;

                        if (isset($ingredientsNeeded[$subIngredientId])) {
                            $ingredientsNeeded[$subIngredientId]['amount'] += $subMeasurementAmount;
                            $ingredientsNeeded[$subIngredientId]['total_price'] += $subIngredientTotalPrice;
                        } else {
                            $ingredientsNeeded[$subIngredientId] = [
                                'id' => $subIngredientId,
                                'title' => $subIngredientTitle,
                                'measurement_type' => $subMeasurementType,
                                'amount' => $subMeasurementAmount,
                                'price_per_unit' => $subIngredientPrice,
                                'total_price' => $subIngredientTotalPrice,
                            ];
                        }

                        $totalPrice += $subIngredientTotalPrice;
                    }
                }
            }
        }

        return response()->json([
            'ingredients' => array_values($ingredientsNeeded),
            'total_price' => round($totalPrice, 2)
        ]);
    }

    public function getEntityById($userId, $id)
    {
        $dish = Entity::where('user_id', $userId)->findOrFail($id);
        $ingredients = EntityMap::with('child')->where('parent_id', $id)->get();

        $ingrArr = [];

        foreach ($ingredients as $ingredient) {
            $ingrArr[]= [
                'id' => $ingredient->child->id,
                'measurement_type' => $ingredient->child->measurement_type,
                'measurement_amount' => $ingredient->child->measurement_amount,
                'title' => $ingredient->child->title,
            ];
        }
        return response()->json([
            'entity' => $dish,
            'ingredients' => $ingrArr
        ]);
    }

    public function updateEntityIngredients($userId, $id, $ingredientsData)
    {
        $dish = Entity::where('user_id', $userId)->findOrFail($id);
    
        DB::transaction(function () use ($id, $ingredientsData) {
            EntityMap::where('parent_id', $id)->delete();
    
            foreach ($ingredientsData as $ingredient) {
                EntityMap::create([
                    'parent_id' => $id,
                    'child_id' => $ingredient['id'],
                    'measurement_type' => $ingredient['measurement_type'],
                    'measurement_amount' => $ingredient['measurement_amount']
                ]);
            }
        });
    
        return response()->json(['message' => 'Dish updated successfully']);
    }

    public function getAllEntities($userId, $filters = [])
    {
        $query = Entity::where('user_id', $userId);

        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        return response()->json($query->get());
    }

    public function createEntity($data, $userId)
    {
        $entity = Entity::create([
            'title' => $data['title'],
            'type' => $data['type'],
            'measurement_type' => $data['measurement_type'],
            'measurement_amount' => 1,
            'price' => $data['price'] ?? null,
            'user_id' => $userId ?? null
        ]);

        $ingredients = $data['ingredients'] ?? [];
    
        foreach ($ingredients as $ingredient) {
            EntityMap::create([
                'parent_id' => $entity->id,
                'child_id' => $ingredient['id'],
                'measurement_type' => $ingredient['measurement_type'],
                'measurement_amount' => $ingredient['measurement_amount'],
            ]);
        }
    
        return response()->json(['message' => 'Dish created successfully'], 201);
    }

    public function deleteEntity($userId, $id)
    {
        $dish = Entity::where('user_id', $userId)->findOrFail($id);
        
        EntityMap::where('child_id', $id)
            ->orWhere('parent_id', $id)
            ->delete();
    
        $dish->delete();
    
        return response()->json(['message' => 'Dish deleted successfully'], 201);
    }

    public function getIngredients($userId)
    {
        $ingredients = Entity::where('type', 'ingredient')
            ->get();
    
        return $ingredients;
    }

    public function updateIngredient($userId, $id, $data)
    {
        return Entity::where('id', $id)
            ->where('user_id', $userId)
            ->update([
                'title' => $data['title'],
                'price' => $data['price'],
                'measurement_type' => $data['measurement_type']
            ]);
    }

    public function addIngredient($userId, $data)
    {
        Entity::create([
            'title' => $data['title'],
            'type' => 'ingredient',
            'measurement_type' => $data['measurement_type'],
            'measurement_amount' => 1,
            'price' => $data['price'] ?? null,
            'user_id' => $userId
        ]);
    
        return response()->json(['message' => 'Ingredient added successfully'], 201);
    }

    public function getDishes($userId)
    {
        $dishes = Entity::where('type', 'dish')
            ->where('user_id', $userId)
            ->get();
    
        return $dishes;
    }

    public function getMixes($userId)
    {
        $mixes = Entity::where('type', 'mix')
            ->where('user_id', $userId)
            ->get();
    
        return $mixes;
    }
}

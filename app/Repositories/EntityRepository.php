<?php

namespace App\Repositories;

use App\Models\Entity;
use App\Models\EntityMap;
use Illuminate\Support\Facades\DB;

class EntityRepository
{
    public function getWithIngredients()
    {
        $entities = Entity::where('type', 'dish')
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

    public function getEntityById($id)
    {
        $entity = Entity::findOrFail($id);

        return $entity;
    }

    public function getAllEntities($filters = [])
    {
        $query = Entity::query();

        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        return response()->json($query->get());
    }

    public function createEntity($data)
    {
        $entity = Entity::create([
            'title' => $data['title'],
            'type' => $data['type'],
            'measurement_type' => $data['measurement_type'],
            'measurement_amount' => 1,
            'price' => $data['price'] ?? null,
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
    
        return response()->json([
            'message' => 'Entity created successfully',
            'id' => $entity->id
        ], 201);
    }

    public function deleteEntity($id)
    {
        $dish = Entity::findOrFail($id);
        
        EntityMap::where('child_id', $id)
            ->orWhere('parent_id', $id)
            ->delete();
    
        $dish->delete();
    
        return response()->json(['message' => 'entity deleted successfully'], 201);
    }

    public function getIngredients()
    {
        $ingredients = Entity::where('type', 'ingredient')
            ->paginate();
    
        return $ingredients;
    }

    public function updateIngredient($id, $data)
    {
        return Entity::where('id', $id)
            ->where('type', 'ingredient')
            ->update($data);
    }

    public function updateDish($id, $data, $ingredients)
    {
        $entity = Entity::findOrFail($id);
        $entity->update($data);
    
        $existingIngredients = EntityMap::where('parent_id', $id)->get()->keyBy('child_id');
    
        foreach ($ingredients as $ingredient) {
            if (isset($existingIngredients[$ingredient['id']])) {
                $existingIngredient = $existingIngredients[$ingredient['id']];
                $existingIngredient->update([
                    'measurement_amount' => $ingredient['measurement_amount'],
                ]);
            } else {
                EntityMap::create([
                    'parent_id' => $entity->id,
                    'child_id' => $ingredient['id'],
                    'measurement_amount' => $ingredient['measurement_amount'],
                    'measurement_type' => $ingredient['measurement_type'] ?? 'unit',
                ]);
            }
        }
    
        $ingredientIds = array_column($ingredients, 'id');
        EntityMap::where('parent_id', $id)
            ->whereNotIn('child_id', $ingredientIds)
            ->delete();
    
        return response()->json(['message' => 'Dish updated successfully'], 201);
    }
    

    public function addIngredient($data)
    {
        Entity::create([
            'title' => $data['title'],
            'type' => 'ingredient',
            'measurement_type' => $data['measurement_type'],
            'measurement_amount' => 1,
            'price' => $data['price'] ?? null,
        ]);
    
        return response()->json(['message' => 'Ingredient added successfully'], 201);
    }

    public function getDishes()
    {
        $dishes = Entity::where('entities.type', 'dish')
            ->leftJoin('entity_maps', 'entities.id', '=', 'entity_maps.parent_id')
            ->leftJoin('entities as child_entities', 'entity_maps.child_id', '=', 'child_entities.id')
            ->select(
                'entities.*',
                DB::raw('SUM(child_entities.price * entity_maps.measurement_amount) as ingredients_cost')
            )
            ->groupBy('entities.id')
            ->paginate();

        $dishes->makeVisible('ingredients_cost');
    
        return $dishes;
    }

    public function getMixes()
    {
        $mixes = Entity::where('type', 'mix')
            ->get();
    
        return $mixes;
    }

    public function getDishByID($id)
    {
        $dish = DB::table('entities')
            ->leftJoinSub(
                DB::table('entity_maps as em')
                    ->join('entities as child_entities', 'em.child_id', '=', 'child_entities.id')
                    ->select(
                        'em.parent_id',
                        DB::raw('SUM(child_entities.price * em.measurement_amount) as total_price'),
                        DB::raw('JSON_ARRAYAGG(JSON_OBJECT("id", child_entities.id, "title", child_entities.title, "type", child_entities.type, "measurement_type", em.measurement_type, "measurement_amount", em.measurement_amount, "price", child_entities.price)) as children')
                        )
                    ->groupBy('em.parent_id'),
                'child_data',
                'entities.id',
                '=',
                'child_data.parent_id'
            )->select(
                'entities.id',
                'entities.title',
                'entities.price',
                'entities.measurement_type',
                'entities.measurement_amount',
                'entities.config',
                DB::raw('COALESCE(child_data.children, JSON_ARRAY()) as ingredients'),
                DB::raw('COALESCE(child_data.total_price, 0) as ingredients_cost')
            )
            ->where('id', $id)
            ->where('type', 'dish')
            ->first();

        $dish->ingredients = json_decode($dish->ingredients, true);
       
        return response()->json($dish);
    }
}

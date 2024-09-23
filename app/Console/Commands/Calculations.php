<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Entity;
use App\Models\EntityMap;

class Calculations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:calculations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $objects = [];
        $distinctParentIDs = EntityMap::select('parent_id')
            ->distinct('parent_id')
            ->get()
            ->pluck('parent_id');

        $dishes = Entity::whereIn('id', $distinctParentIDs)
            ->get();

        $choices = $dishes->pluck('title', 'id')->toArray();
        $choices['end'] = 'End';

        while (true) {
            // Provide select options for type
            $selectedId = $this->choice('Select type (or choose "end" to finish)', $choices);

            if ($selectedId === 'end') {
                break;
            }

            $quantityInput = $this->ask('Enter quantity');

            // Validate the quantity input and convert it to a float
            if (is_numeric($quantityInput)) {
                $quantity = (float)$quantityInput;
                $objects[] = ['type' => $selectedId, 'quantity' => $quantity];
            } else {
                $this->error('Please enter a valid number for quantity.');
            }
        }

        $outputArray = [];

        foreach ($objects as $key => $value) {
            $ingredients = EntityMap::join('entities', 'entities.id', 'entity_maps.child_id')
                ->select('entities.title', 'entity_maps.measurement_amount', 'entities.type')
                ->where('entity_maps.parent_id', $value['type'])
                ->get();

            foreach ($ingredients as $ingredient) {
                if ($ingredient->type == 'mix') {
                    $subIngredients = EntityMap::join('entities', 'entities.id', 'entity_maps.child_id')
                        ->select('entities.title', 'entity_maps.measurement_amount')
                        ->where('entity_maps.parent_id', $ingredient->id)
                        ->get();

                    if (!isset($outputArray[$subIngredients['title']])) {
                        $outputArray[$subIngredients['title']] = floatVal($subIngredients['measurement_amount']);
                    }else{
                        $outputArray[$subIngredients['title']] += floatVal($subIngredients['measurement_amount']);
                    }
                }
                if (!isset($outputArray[$ingredient['title']])) {
                    $outputArray[$ingredient['title']] = floatVal($ingredient['measurement_amount']);
                }else{
                    $outputArray[$ingredient['title']] += floatVal($ingredient['measurement_amount']);
                }
            }            
        }

        dd($outputArray);

    }
}

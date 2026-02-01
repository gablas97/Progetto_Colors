<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // Categorie principali
        $cancelleria = Category::create([
            'name' => 'Cancelleria',
            'slug' => 'cancelleria',
            'description' => 'Prodotti di cancelleria per scuola e ufficio',
            'order' => 1,
            'is_active' => true,
        ]);

        $scuola = Category::create([
            'name' => 'Scuola',
            'slug' => 'scuola',
            'description' => 'Tutto per la scuola',
            'order' => 2,
            'is_active' => true,
        ]);

        $ufficio = Category::create([
            'name' => 'Ufficio',
            'slug' => 'ufficio',
            'description' => 'Articoli per ufficio',
            'order' => 3,
            'is_active' => true,
        ]);

        // Sottocategorie di Cancelleria
        $quaderni = Category::create([
            'name' => 'Quaderni',
            'slug' => 'quaderni',
            'parent_id' => $cancelleria->id,
            'order' => 1,
        ]);

        Category::create([
            'name' => 'Quaderni A4',
            'slug' => 'quaderni-a4',
            'parent_id' => $quaderni->id,
            'order' => 1,
        ]);

        Category::create([
            'name' => 'Quaderni A5',
            'slug' => 'quaderni-a5',
            'parent_id' => $quaderni->id,
            'order' => 2,
        ]);

        $penne = Category::create([
            'name' => 'Penne',
            'slug' => 'penne',
            'parent_id' => $cancelleria->id,
            'order' => 2,
        ]);

        Category::create([
            'name' => 'Penne Biro',
            'slug' => 'penne-biro',
            'parent_id' => $penne->id,
            'order' => 1,
        ]);

        Category::create([
            'name' => 'Penne Stilografiche',
            'slug' => 'penne-stilografiche',
            'parent_id' => $penne->id,
            'order' => 2,
        ]);

        $matite = Category::create([
            'name' => 'Matite',
            'slug' => 'matite',
            'parent_id' => $cancelleria->id,
            'order' => 3,
        ]);

        // Sottocategorie di Scuola
        Category::create([
            'name' => 'Zaini',
            'slug' => 'zaini',
            'parent_id' => $scuola->id,
            'order' => 1,
        ]);

        Category::create([
            'name' => 'Astucci',
            'slug' => 'astucci',
            'parent_id' => $scuola->id,
            'order' => 2,
        ]);

        // Sottocategorie di Ufficio
        Category::create([
            'name' => 'Raccoglitori',
            'slug' => 'raccoglitori',
            'parent_id' => $ufficio->id,
            'order' => 1,
        ]);

        Category::create([
            'name' => 'Cartelline',
            'slug' => 'cartelline',
            'parent_id' => $ufficio->id,
            'order' => 2,
        ]);

        // Categoria Promozioni (trasversale)
        Category::create([
            'name' => 'Promozioni',
            'slug' => 'promozioni',
            'description' => 'Prodotti in offerta',
            'order' => 99,
            'is_active' => true,
        ]);
    }
}
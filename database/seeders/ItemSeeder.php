<?php

namespace Database\Seeders;

use App\Models\Item;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $items = include database_path('seeders/data/items.php');

        Item::insert($items);

        // //
        // $file = fopen(database_path('seeders/items.csv'), 'r');
        // $i = 0;
        // $keys = [];
        // $completed = [];
        // while (($line = fgetcsv($file)) !== false) {
        //     if ($i === 0) {
        //         $keys = $line;
        //         // dd(array_flip($keys));

        //         $i++;

        //         continue;
        //     }

        //     $completed[] = array_combine($keys, $line);
        //     $i++;
        // }
        // fclose($file);
        // $arrayString = '<?php return '.var_export($completed, true).';';

        // // Write the string representation to a PHP file
        // $outputFile = database_path('seeders/items.php');
        // file_put_contents($outputFile, $arrayString);

    }
}

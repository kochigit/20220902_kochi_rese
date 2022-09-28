<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Restaurant;
use Goodby\CSV\Import\Standard\Lexer;
use Goodby\CSV\Import\Standard\Interpreter;
use Goodby\CSV\Import\Standard\LexerConfig;
use Illuminate\Support\Str;

class RestaurantSeeder extends Seeder
{
    const CSV_FILENAME = '/../database/seeds/restaurants-seed.csv';

    public function run()
    {
        $this->command->info('[Start] import data.');

        $config = new LexerConfig();
        // セパレーター指定、"\t"を指定すればtsvファイルも取り込み可
        $config->setDelimiter(",");
        $config->setIgnoreHeaderLine(true);
        $lexer = new Lexer($config);
        $interpreter = new Interpreter();
        $interpreter->addObserver(function (array $row) {
            Restaurant::create([
                'uuid' => (string) Str::uuid(),
                'name' => $row[0],
                'area' => $row[1],
                'genre' => $row[2],
                'description' => $row[3],
                'img_path' => config('app.url').$row[4],
            ]);
        });

        $lexer->parse(app_path() . self::CSV_FILENAME, $interpreter);

        $this->command->info('[End] import data.');
    }
}

<?php

namespace Database\Factories;

use App\Models\CarModel;
use Illuminate\Database\Eloquent\Factories\Factory;

class CarModelFactory extends Factory
{
    protected $model = CarModel::class;

    public function definition()
    {
        $grades = ['コンパクト', 'ミドル', 'ラグジュアリー'];
        return [
            'name' => $this->faker->randomElement(['フィット', 'ノート', 'アクア', 'プリウス', 'カローラ', 'ヴィッツ', 'デミオ', 'マーチ']),
            'grade' => $this->faker->randomElement($grades),
            'description' => $this->faker->paragraph(),
        ];
    }
}

<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model\Todo\TodoCategory;
use Faker\Generator as Faker;

$factory->define(TodoCategory::class, function (Faker $faker) {
    $title = $faker->text(20);
    return [
        'title' => $title,
        'slug' => Str::slug($title),
        'description' => $faker->text(100),
        'parent_id' => $faker->numberBetween(0, 6),
        'user_id' => $faker->numberBetween(0, 6),
    ];
});

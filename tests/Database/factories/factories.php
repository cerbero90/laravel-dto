<?php

use Cerbero\LaravelDto\Database\Models\Tests\User;
use Cerbero\LaravelDto\Database\Models\Tests\Post;
use Cerbero\LaravelDto\Database\Models\Tests\Comment;
use Cerbero\LaravelDto\Database\Models\Tests\Image;
use Faker\Generator as Faker;
use Illuminate\Support\Str;


$factory->define(User::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', // secret
        'remember_token' => Str::random(10),
    ];
});

$factory->define(Post::class, function (Faker $faker) use ($factory) {
    return [
        'is_private' => $faker->boolean,
        'title' => $faker->words(3, true),
        'body' => $faker->sentences(2, true),
        'user_id' => $factory->create(User::class)->id,
    ];
});

$factory->define(Comment::class, function (Faker $faker) use ($factory) {
    return [
        'body' => $faker->sentences(2, true),
        'user_id' => $factory->create(User::class)->id,
        'post_id' => $factory->create(Post::class)->id,
    ];
});

$factory->define(Image::class, function (Faker $faker) {
    return [
        'url' => $faker->url,
    ];
});

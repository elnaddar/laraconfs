<?php

namespace Database\Factories;

use App\Enums\Region;
use App\Models\Conference;
use App\Models\Venue;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ConferenceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Conference::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $startDate = now()->addMonths(9);
        $endDate = $startDate->addDays(2);
        return [
            'name' => fake()->name(),
            'description' => fake()->text(),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => fake()->randomElement(["draft", "published", "archived"]),
            'region' => fake()->randomElement(Region::class),
            'venue_id' => Venue::factory(),
        ];
    }
}

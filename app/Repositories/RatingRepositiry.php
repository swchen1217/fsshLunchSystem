<?php

namespace App\Repositories;

use App\Entity\Rating;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class RatingRepositiry
{
    /**
     * @var Rating
     */
    private $rating;

    public function __construct(Rating $rating)
    {
        $this->rating = $rating;
    }

    public function findByDishId($dish_id)
    {
        return $this->rating->where('dish_id', $dish_id)->get();
    }

    public function getAverageByDishId($dish_id)
    {
        return Cache::tags('rating')->remember($dish_id, Carbon::now()->addHours(3), function() {

        });
    }

    public function create($user_id, $dish_id, $rating)
    {
        Cache::tags('rating')->forget($dish_id);
        return $this->rating->create(['user_id' => $user_id, 'dish_id' => $dish_id, 'rating' => $rating]);
    }
}

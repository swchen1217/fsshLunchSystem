<?php

namespace App\Repositories;

use App\Entity\Rating;

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

    }

    public function create($user_id, $dish_id, $rating)
    {

    }
}

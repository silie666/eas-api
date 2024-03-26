<?php

namespace Database\Factories\Card;

use App\Models\Card\Card;
use App\Models\User\Student;
use Carbon\Carbon;
use Database\Factories\BaseFactory;

class CardFactory extends BaseFactory
{
    protected $model = Card::class;

    public function definition()
    {
        $faker = $this->faker;
        return [
            'brand_name'      => $faker->iban,
            'number'          => $faker->creditCardNumber,
            'expiration_date' => Carbon::now(),
            'card_table_id'   => $this->firstStudent()->id,
            'card_table_type' => Student::class,
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Card $card) {
        });
    }
}
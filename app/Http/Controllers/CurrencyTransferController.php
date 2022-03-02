<?php

namespace App\Http\Controllers;

use App\Enums\ValidCurrencyType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;

class CurrencyTransferController extends Controller
{

    public function __invoke(Request $request)
    {
        $input = $request->validate([
            'source_currency' => ['required', new Enum(ValidCurrencyType::class)],
            'target_currency' => ['required', new Enum(ValidCurrencyType::class)],
            'source_amount' => ['required', 'numeric', 'min:0'],
        ]);

        $exchangeRate = config("app.currencies.{$input['source_currency']}.{$input['target_currency']}");

        $rawAmount = bcmul($input['source_amount'], $exchangeRate, 6);
        $roundAmount = round($rawAmount, 2);
        $transferredAmount = number_format($roundAmount, 2, '.', ',');

        return [
            'data' => [
                'target_currency' => $input['target_currency'],
                'source_currency' => $input['source_currency'],
                'source_amount' => $input['source_amount'],
                'transferred_amount' => $transferredAmount
            ]
        ];
    }

    private function getRandomCurrencyType()
    {
        return array_rand((new \ReflectionClass(ValidCurrencyType::class))->getConstants());
    }
}

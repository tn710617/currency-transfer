<?php

namespace Tests\Feature;

use App\Enums\ValidCurrencyType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CurrencyTransferTest extends TestCase
{

    public function test_twd_currency_can_be_transferred_to_jpy()
    {
        $this->testCurrencyTransferring(ValidCurrencyType::TWD->value, ValidCurrencyType::JPY->value);
    }

    public function test_twd_currency_can_be_transferred_to_usd()
    {
        $this->testCurrencyTransferring(ValidCurrencyType::TWD->value, ValidCurrencyType::USD->value);
    }

    public function test_twd_currency_can_be_transferred_to_twd()
    {
        $this->testCurrencyTransferring(ValidCurrencyType::TWD->value, ValidCurrencyType::TWD->value);
    }

    public function test_jpy_currency_can_be_transferred_to_twd()
    {
        $this->testCurrencyTransferring(ValidCurrencyType::JPY->value, ValidCurrencyType::TWD->value);
    }

    public function test_jpy_currency_can_be_transferred_to_usd()
    {
        $this->testCurrencyTransferring(ValidCurrencyType::JPY->value, ValidCurrencyType::USD->value);
    }

    public function test_jpy_currency_can_be_transferred_to_jpy()
    {
        $this->testCurrencyTransferring(ValidCurrencyType::JPY->value, ValidCurrencyType::JPY->value);
    }

    public function test_usd_currency_can_be_transferred_to_twd()
    {
        $this->testCurrencyTransferring(ValidCurrencyType::USD->value, ValidCurrencyType::TWD->value);
    }

    public function test_usd_currency_can_be_transferred_to_jpy()
    {
        $this->testCurrencyTransferring(ValidCurrencyType::USD->value, ValidCurrencyType::JPY->value);
    }

    public function test_usd_currency_can_be_transferred_to_usd()
    {
        $this->testCurrencyTransferring(ValidCurrencyType::USD->value, ValidCurrencyType::USD->value);
    }

    public function test_invalid_source_currency_type_would_fail()
    {
        $response = $this->json('get', '/api/transferred-amount', [
            'source_amount' => $this->getRandomFloat(0, 10000),
            'source_currency' => 'wrong_currency_type',
            'target_currency' => ValidCurrencyType::USD->value
        ]);

        $response->assertStatus(422);
    }

    public function test_invalid_target_currency_type_would_fail()
    {
        $response = $this->json('get', '/api/transferred-amount', [
            'source_amount' => $this->getRandomFloat(0, 10000),
            'source_currency' => ValidCurrencyType::JPY->value,
            'target_currency' => 'wrong_currency_type'
        ]);

        $response->assertStatus(422);
    }

    public function test_invalid_source_amount_format_would_fail()
    {
        $response = $this->json('get', '/api/transferred-amount', [
            'source_amount' => 'wrong_source_amount',
            'source_currency' => ValidCurrencyType::JPY->value,
            'target_currency' => ValidCurrencyType::USD->value
        ]);

        $response->assertStatus(422);
    }

    private function testCurrencyTransferring(string $sourceCurrency, string $targetCurrency)
    {
        $sourceAmount = $this->getRandomFloat(0, 10000);
        $assertedTransferredAmount = $this->getTransferredAmount($sourceAmount, $sourceCurrency,
            $targetCurrency);

        $response = $this->json('get', '/api/transferred-amount', [
            'source_amount' => $sourceAmount,
            'source_currency' => $sourceCurrency,
            'target_currency' => $targetCurrency
        ]);

        $response->assertStatus(200);
        $transferredAmount = $response->json()['data']['transferred_amount'];
        $this->assertSame($transferredAmount, $assertedTransferredAmount);
    }

    private function getTransferredAmount(float $sourceAmount, string $sourceCurrency, string $targetCurrency): string
    {
        $exchangeRate = config("app.currencies.{$sourceCurrency}.{$targetCurrency}");

        $rawAmount = bcmul($sourceAmount, $exchangeRate, 6);
        $roundAmount = round($rawAmount, 2);
        $transferredAmount = number_format($roundAmount, 2, '.', ',');

        return $transferredAmount;
    }

    private function getRandomFloat(int $stNum = 0, int $endNum = 1, $mul = 1000000)
    {
        if ($stNum > $endNum) {
            return false;
        }

        return mt_rand($stNum * $mul, $endNum * $mul) / $mul;
    }
}

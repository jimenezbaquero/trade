<?php

namespace Database\Seeders;

use App\Models\Exchange;
use App\Models\Pair;
use App\Models\TradingPair;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PairSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $binance = Exchange::where('slug', 'binance')->first();

        if (!$binance) {
            $this->command->error('Binance exchange not found.');
            return;
        }

        $pairs = [

            // BTC
            [
                'symbol' => 'BTCUSDT',
                'base_asset' => 'BTC',
                'quote_asset' => 'USDT',
                'status' => 'TRADING',
                'price_precision' => 2,
                'quantity_precision' => 5,
                'min_qty' => 0.00001,
                'max_qty' => 9000,
                'tick_size' => 0.01,
                'step_size' => 0.00001,
                'min_notional' => 10,
                'metadata' => [
                    'type' => 'major',
                ],
            ],

            // ETH
            [
                'symbol' => 'ETHUSDT',
                'base_asset' => 'ETH',
                'quote_asset' => 'USDT',
                'status' => 'TRADING',
                'price_precision' => 2,
                'quantity_precision' => 4,
                'min_qty' => 0.0001,
                'max_qty' => 100000,
                'tick_size' => 0.01,
                'step_size' => 0.0001,
                'min_notional' => 10,
                'metadata' => [
                    'type' => 'major',
                ],
            ],

            // BNB
            [
                'symbol' => 'BNBUSDT',
                'base_asset' => 'BNB',
                'quote_asset' => 'USDT',
                'status' => 'TRADING',
                'price_precision' => 2,
                'quantity_precision' => 3,
                'min_qty' => 0.001,
                'max_qty' => 100000,
                'tick_size' => 0.01,
                'step_size' => 0.001,
                'min_notional' => 10,
                'metadata' => [
                    'type' => 'major',
                ],
            ],

            // SOL
            [
                'symbol' => 'SOLUSDT',
                'base_asset' => 'SOL',
                'quote_asset' => 'USDT',
                'status' => 'TRADING',
                'price_precision' => 2,
                'quantity_precision' => 2,
                'min_qty' => 0.01,
                'max_qty' => 100000,
                'tick_size' => 0.01,
                'step_size' => 0.01,
                'min_notional' => 10,
                'metadata' => [
                    'type' => 'major',
                ],
            ],

            // XRP
            [
                'symbol' => 'XRPUSDT',
                'base_asset' => 'XRP',
                'quote_asset' => 'USDT',
                'status' => 'TRADING',
                'price_precision' => 4,
                'quantity_precision' => 1,
                'min_qty' => 1,
                'max_qty' => 1000000,
                'tick_size' => 0.0001,
                'step_size' => 1,
                'min_notional' => 10,
                'metadata' => [
                    'type' => 'major',
                ],
            ],

        ];

        foreach ($pairs as $pair) {

            Pair::updateOrCreate(
                [
                    'exchange_id' => $binance->id,
                    'symbol' => $pair['symbol'],
                ],
                [
                    'base_asset' => $pair['base_asset'],
                    'quote_asset' => $pair['quote_asset'],
                    'status' => $pair['status'],
                    'price_precision' => $pair['price_precision'],
                    'quantity_precision' => $pair['quantity_precision'],
                    'min_qty' => $pair['min_qty'],
                    'max_qty' => $pair['max_qty'],
                    'tick_size' => $pair['tick_size'],
                    'step_size' => $pair['step_size'],
                    'min_notional' => $pair['min_notional'],
                    'metadata' => $pair['metadata'],
                    'is_active' => true,
                ]
            );
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\Exchange;
use App\Models\TradingPair;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TradingPairSeeder extends Seeder
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
            // MAJORS
            ['BTCUSDT', 'BTC', 'USDT'],
            ['ETHUSDT', 'ETH', 'USDT'],
            ['BNBUSDT', 'BNB', 'USDT'],
            ['SOLUSDT', 'SOL', 'USDT'],
            ['XRPUSDT', 'XRP', 'USDT'],
            
            // STRONG ALTS
            ['ADAUSDT', 'ADA', 'USDT'],
            ['DOGEUSDT', 'DOGE', 'USDT'],
            ['AVAXUSDT', 'AVAX', 'USDT'],
            ['LINKUSDT', 'LINK', 'USDT'],
            ['DOTUSDT', 'DOT', 'USDT'],
            
            // AI / TRENDING
            ['FETUSDT', 'FET', 'USDT'],
            ['RNDRUSDT', 'RNDR', 'USDT'],
            ['INJUSDT', 'INJ', 'USDT'],
            
            // MEME / HIGH VOL
            ['PEPEUSDT', 'PEPE', 'USDT'],
            ['SHIBUSDT', 'SHIB', 'USDT'],
        ];
        
        foreach ($pairs as [$symbol, $base, $quote]) {
            
            TradingPair::updateOrCreate(
                [
                    'exchange_id' => $binance->id,
                    'symbol' => $symbol,
                ],
                [
                    'base_asset' => $base,
                    'quote_asset' => $quote,
                    'is_active' => true,
                ]
            );
        }
    }
}

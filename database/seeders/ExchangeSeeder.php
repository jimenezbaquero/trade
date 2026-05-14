<?php

namespace Database\Seeders;

use App\Models\Exchange;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExchangeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $exchanges = [
            [
                'name' => 'Binance',
                'slug' => 'binance',
                'api_url' => 'https://api.binance.com',
                'testnet_api_url' => 'https://testnet.binance.vision',
                'websocket_url' => 'wss://stream.binance.com:9443/ws',
                'testnet_websocket_url' => 'wss://testnet.binance.vision/ws',
                'rate_limit' => 1200,
                'metadata' => [
                    'type' => 'spot',
                    'fees' => [
                        'maker' => 0.001,
                        'taker' => 0.001,
                    ],
                    'supports_futures' => true,
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Bybit',
                'slug' => 'bybit',
                'api_url' => 'https://api.bybit.com',
                'testnet_api_url' => 'https://api-testnet.bybit.com',
                'websocket_url' => 'wss://stream.bybit.com/v5/public/spot',
                'testnet_websocket_url' => 'wss://stream-testnet.bybit.com/v5/public/spot',
                'rate_limit' => 600,
                'metadata' => [
                    'type' => 'futures',
                    'supports_futures' => true,
                    'leverage_max' => 100,
                ],
                'is_active' => true,
            ],
            [
                'name' => 'OKX',
                'slug' => 'okx',
                'api_url' => 'https://www.okx.com/api/v5',
                'testnet_api_url' => 'https://www.okx.com/api/v5',
                'websocket_url' => 'wss://ws.okx.com:8443/ws/v5/public',
                'testnet_websocket_url' => 'wss://wspap.okx.com:8443/ws/v5/public',
                'rate_limit' => 300,
                'metadata' => [
                    'type' => 'spot_futures',
                    'supports_options' => true,
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Kraken',
                'slug' => 'kraken',
                'api_url' => 'https://api.kraken.com',
                'testnet_api_url' => null,
                'websocket_url' => 'wss://ws.kraken.com',
                'testnet_websocket_url' => null,
                'rate_limit' => 60,
                'metadata' => [
                    'type' => 'spot',
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Coinbase Advanced',
                'slug' => 'coinbase',
                'api_url' => 'https://api.coinbase.com',
                'testnet_api_url' => 'https://api-public.sandbox.exchange.coinbase.com',
                'websocket_url' => 'wss://ws-feed.exchange.coinbase.com',
                'testnet_websocket_url' => 'wss://ws-feed-public.sandbox.exchange.coinbase.com',
                'rate_limit' => 100,
                'metadata' => [
                    'type' => 'spot',
                    'regulated' => true,
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Bitget',
                'slug' => 'bitget',
                'api_url' => 'https://api.bitget.com',
                'testnet_api_url' => 'https://api-testnet.bitget.com',
                'websocket_url' => 'wss://ws.bitget.com/mix/v1/stream',
                'testnet_websocket_url' => 'wss://ws-testnet.bitget.com/mix/v1/stream',
                'rate_limit' => 300,
                'metadata' => [
                    'type' => 'futures',
                    'copy_trading' => true,
                ],
                'is_active' => false,
            ],
        ];
        
        
        foreach ($exchanges as $exchange) {
            Exchange::create($exchange);
        }
    }
}

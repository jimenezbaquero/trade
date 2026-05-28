<?php

namespace App\Console\Commands;

use App\Events\CandleChanged;
use App\Managers\ExchangeManager;
use App\Models\Candle;
use App\Models\Exchange;
use App\Models\Pair;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('candles:get {exchange} {symbol} {interval} {--from=} {--to=}')]
#[Description('Command to get candles from an exchange')]
class GetCandles extends Command {
    /**
     * Execute the console command.
     */
    public function handle(ExchangeManager $exchangeManager) {
        $exchange = $this->argument('exchange');
        $this->info('exchange ' . $exchange);

        $exchange = Exchange::where('slug', 'like', $exchange)->firstOrFail();

        $symbol = $this->argument('symbol');
        $interval = $this->argument('interval');
        $fromOption = $this->option('from');
        $to = $this->option('to') ? strtotime($this->option('to')) * 1000 : now()->getTimestampMs();

        $pair = $exchange->pairs()->where('symbol', $symbol)->firstOrFail();
        $live = $interval == 'live';
        
        if ($interval == 'live') {
            $intervals = [
                '1m',
                '5m',
                '15m',
                '1h'
            ];
        }

        $client = $exchangeManager->make($exchange);

        if(!$live) {
            $this->updateCandles($client, $live, $pair, $symbol, $interval, $fromOption, $to);
        }else {
            while (true) {
                $to = now()->getTimestampMs();
                foreach ($intervals as $interval) {
                    $this->updateCandles($client, $live, $pair, $symbol, $interval, $fromOption, $to);
                }
                sleep(2);
            }
        }
        $this->info("Done.");
    }

    public function updateCandles($client, $live, Pair $pair, string $symbol, string $interval, $fromOption, $to) {
        if (!$fromOption) {
            $lastCandle = $pair->candles()->where('timeframe', $interval)->latest('opened_at')->first();
            if ($lastCandle) {
                $from = $lastCandle->opened_at
                    ->subMinute()
                    ->getTimestampMs();
            } else {
                $from = now()
                    ->subDay()
                    ->getTimestampMs();
            }
        } else {
            $from = strtotime($fromOption) * 1000;
        }

        while ($from <= $to) {
            $klines = $client->klines(
                $symbol,
                $interval,
                $from,
                $to,
                1000
            );

            if (empty($klines)) {
                break;
            }

            $batch = [];
            $numberKlines = count($klines);
            
            foreach ($klines as $index => $k) {
                $isLast = $index === $numberKlines - 1;

                $batch[] = [
                    'pair_id' => $pair->id,
                    'timeframe' => $interval,

                    'opened_at' => date('Y-m-d H:i:s', $k[0] / 1000),
                    'closed_at' => date('Y-m-d H:i:s', $k[6] / 1000),
                    'is_closed' => !$isLast,

                    'open' => $k[1],
                    'high' => $k[2],
                    'low' => $k[3],
                    'close' => $k[4],

                    'volume' => $k[5],
                    'quote_volume' => $k[7] ?? null,

                    'trades_count' => $k[8] ?? null,

                    'taker_buy_base_volume' => $k[9] ?? null,
                    'taker_buy_quote_volume' => $k[10] ?? null,

                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            
            if(!$live) {
                Candle::upsert(
                    $batch,
                    [
                        'pair_id',
                        'timeframe',
                        'opened_at'
                    ],
                    [
                        'closed_at',
                        'is_closed',
                        'open',
                        'high',
                        'low',
                        'close',
                        'volume',
                        'quote_volume',
                        'trades_count',
                        'taker_buy_base_volume',
                        'taker_buy_quote_volume',
                        'updated_at'
                    ]
                );
            }else{
                foreach ($batch as $data){
                    $candle = Candle::updateOrCreate(
                        [
                            'pair_id' => $data['pair_id'],
                            'timeframe' => $data['timeframe'],
                            'opened_at' => $data['opened_at'],
                        ],
                        [
                            'closed_at' => $data['closed_at'],
                            'is_closed' => $data['is_closed'],
                            'open' => $data['open'],
                            'high' => $data['high'],
                            'low' => $data['low'],
                            'close' => $data['close'],
                            'volume' => $data['volume'],
                            'quote_volume' => $data['quote_volume'],
                            'trades_count' => $data['trades_count'],
                            'taker_buy_base_volume' => $data['taker_buy_base_volume'],
                            'taker_buy_quote_volume' => $data['taker_buy_quote_volume'],
                        ]
                    );
                    
                    $dirty = $candle->wasRecentlyCreated || $candle->wasChanged([
                            'open',
                            'high',
                            'low',
                            'close',
                            'volume'
                        ]);
                    
                    if ($dirty) {
                        event (new CandleChanged($candle->id));
                    }
                }
            }
            

            $last = end($klines);
            $from = $last[0] + 1;
        }
    }
}

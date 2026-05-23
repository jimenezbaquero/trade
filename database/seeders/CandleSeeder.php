<?php

namespace Database\Seeders;

use App\Models\Candle;
use App\Models\Pair;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class CandleSeeder extends Seeder
{
    public function run(): void
    {
        $pairs = Pair::all();

        $timeframes = [
            '1m' => 60,
            '5m' => 300,
            '15m' => 900,
            '1h' => 3600,
        ];

        foreach ($pairs as $pair) {

            foreach ($timeframes as $tf => $seconds) {

                $this->generateCandles($pair->id, $tf, $seconds);
            }
        }
    }

    private function generateCandles(int $pairId, string $timeframe, int $stepSeconds): void
    {
        $basePrice = rand(100, 50000); // precio inicial aleatorio
        $startTime = Carbon::now()->subHours(48);

        $candles = [];

        for ($i = 0; $i < 200; $i++) {

            $open = $basePrice;

            // movimiento simulado realista
            $change = (mt_rand(-50, 50) / 100); // -0.50% a +0.50%

            $close = $open + ($open * $change);

            $high = max($open, $close) + ($open * (mt_rand(1, 30) / 1000));
            $low  = min($open, $close) - ($open * (mt_rand(1, 30) / 1000));

            $volume = mt_rand(10, 1000) / 10;

            $openedAt = $startTime->copy()->addSeconds($i * $stepSeconds);
            $closedAt = $openedAt->copy()->addSeconds($stepSeconds);

            $candles[] = [
                'pair_id' => $pairId,
                'timeframe' => $timeframe,
                'opened_at' => $openedAt,
                'closed_at' => $closedAt,
                'open' => $open,
                'high' => $high,
                'low' => $low,
                'close' => $close,
                'volume' => $volume,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // siguiente vela parte del cierre
            $basePrice = $close;
        }

        Candle::insert($candles);
    }
}

<template>
  <Head :title="pair.symbol" />

  <AppLayout :isLoading="isLoading">

    <div class="p-6 space-y-4">

      <!-- HEADER -->
      <div class="flex justify-between items-center">
        <div class="w-1/2">
        <h1 class="text-2xl font-bold">
          {{ pair.symbol }}
        </h1>
        </div>
        <!-- TIMEFRAME SELECT -->
        <BaseSelect
            :options="time_options"
            label="pair.timeframe"
            placeholder="pair.timeframe_placeholder"
            v-model="timeframe"
            @update:modelValue="loadCandles"
            class="w-1/2"
        />
      </div>

      <!-- CHART -->
      <div
          ref="chartContainer"
          class="w-full h-[500px] bg-white rounded-lg shadow"
      ></div>

    </div>

  </AppLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import AppLayout from "@/Layouts/AppLayout.vue";
import BaseSelect from "@/Components/BaseSelect.vue";
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { createChart } from 'lightweight-charts'

const props = defineProps({
  pair: Object,
  time_options: Object
})

const chartContainer = ref(null)

const chart = ref(null)
const candleSeries = ref(null)

const timeframe = ref('1m')
const isLoading = ref(false)

function initChart() {
  chart.value = createChart(chartContainer.value, {
    layout: {
      background: { color: '#ffffff' },
      textColor: '#333',
    },
    width: chartContainer.value.clientWidth,
    height: 500,
  })

  candleSeries.value = chart.value.addCandlestickSeries({
    upColor: '#26a69a',
    downColor: '#ef5350',
    borderVisible: false,
    wickUpColor: '#26a69a',
    wickDownColor: '#ef5350',
  })
}

async function loadCandles() {
  isLoading.value = true

  const res = await axios.get(
      route('pairs.candles.get', props.pair.id),
      {
        params: {
          timeframe: timeframe.value,
          limit: 300
        }
      }
  )

  const candles = res.data.map(c => ({
    time: Math.floor(new Date(c.opened_at).getTime() / 1000),
    open: Number(c.open),
    high: Number(c.high),
    low: Number(c.low),
    close: Number(c.close),
  }))

  candleSeries.value.setData(candles)

  isLoading.value = false
}

onMounted(async () => {
  initChart()
  await loadCandles()
})
</script>

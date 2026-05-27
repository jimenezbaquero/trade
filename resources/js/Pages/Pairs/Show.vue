<template>
  <Head :title="pair.symbol"/>

  <AppLayout :isLoading="isLoading">

    <div class="p-6 space-y-4">

      <!-- HEADER -->
      <div class="flex items-center">
        <div class="w-1/2">
          <h1 class="text-2xl font-bold">
            {{ pair.symbol }} - {{ pair.exchange.name }}
          </h1>
        </div>

        <div class="w-1/2 flex justify-between items-center">
          <div class="w-2/3">
          <h1>
            {{ t('pair.last_updated') }} : {{ update_time }}
          </h1>
          <h1>
            {{ t('pair.last_candle') }} : {{ last_updated }}
          </h1>
          </div>
        <BaseSelect
            :options="time_options"
            label="pair.timeframe"
            placeholder="pair.timeframe_placeholder"
            v-model="timeframe"
            @update:modelValue="loadCandles"
            class="w-1/3"
        />
      </div>
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
import {Head} from '@inertiajs/vue3'
import AppLayout from "@/Layouts/AppLayout.vue"
import BaseSelect from "@/Components/BaseSelect.vue"
import {ref, onMounted} from 'vue'
import axios from 'axios'
import {createChart} from 'lightweight-charts'
import {useI18n} from "vue-i18n"

const props = defineProps({
  pair: Object,
  time_options: Object,
})

const {t} = useI18n()

const last_updated = ref('')
const last_candle_time = ref(null)
const update_time = ref('')

const chartContainer = ref(null)

const chart = ref(null)
const candleSeries = ref(null)

const timeframe = ref('1m')
const isLoading = ref(false)

function initChart() {
  chart.value = createChart(chartContainer.value, {
    layout: {
      background: {color: '#ffffff'},
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
        }
      }
  )


  const candles = formatCandles(res.data.candles)

  candleSeries.value.setData(candles)

  last_candle_time.value = candles.length
      ? candles[candles.length - 1].time
      : null

  last_updated.value = new Date(res.data.last_updated * 1000).toLocaleString()

  isLoading.value = false
}

setInterval(async () => {
  console.log('actualizando datos')
  update_time.value = new Date().toLocaleString()
  if (!last_candle_time.value) return

  const res = await axios.get(route('pairs.candles.getLive', props.pair.id), {
    params: {
      timeframe: timeframe.value,
    }
  })

  const newCandles = res.data.candles

  if (newCandles.length) {
    formatCandles(newCandles).forEach((candle) => {
      if (candle.time >= last_candle_time.value) {
        candleSeries.value.update(candle);
        if (candle.time != last_candle_time.value) {
          last_candle_time.value = candle.time;
        }
      }
    })
  }

  if (res.data.last_updated) {
    last_updated.value = new Date(res.data.last_updated * 1000).toLocaleString()
  }

}, 2000)

const formatCandles = (candles) => {
  return candles.map(c => ({
    time: Number(c.opened_at), // 👈 YA ES TIMESTAMP
    open: Number(c.open),
    high: Number(c.high),
    low: Number(c.low),
    close: Number(c.close),
  }))
}

onMounted(async () => {
  initChart()
  await loadCandles()
})
</script>

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
            @update:modelValue="toggleTimeframe"
            class="w-1/3"
          />
        </div>
      </div>

      <div class="mb-4 bg-white p-3 rounded-lg shadow flex flex-wrap gap-3">

        <div
          v-for="indicator in indicators"
          :key="indicator.id"
          class="flex items-center"
        >
          <BaseCheckbox
            :modelValue="isSelected(indicator)"
            :label="indicator.name"
            @update:modelValue="(checked) => toggleIndicator(indicator, checked)"
          />
        </div>

      </div>

      <!-- CHART -->
      <div
        ref="chartContainer"
        class="w-full h-[500px] bg-white rounded-lg shadow"
      ></div>
      <!-- CHART2-->
      <div v-show="showSecondaryChart"
          ref="chartContainer2"
          class="w-full h-[150px] bg-white rounded-lg shadow"
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
import BaseCheckbox from "@/Components/BaseCheckbox.vue";

const props = defineProps({
  pair: Object,
  time_options: Object,
  indicators: Object,
})

const {t} = useI18n()

const last_updated = ref('')
const last_candle_id = ref(null)
const last_candle_time = ref(null)
const update_time = ref('')

const chartContainer = ref(null)
const chartContainer2 = ref(null)

const chart = ref(null)
const chart2 = ref(null)
const candleSeries = ref(null)
const rsiSeries = ref(null)
const historicFirstCandle = ref(null)

const lastFromCandleIndicatorValue = ref({})
const lastToCandleIndicatorValue = ref({})

const indicatorValueSeries = ref({})
const indicatorSelects = ref([])

const timeframe = ref('1m')
const isLoading = ref(false)
const showSecondaryChart = ref(false)

let isRunning = false;
let started = false;

function isSelected(indicator) {
  return indicatorSelects.value.some(i => i.id === indicator.id)
}

async function toggleIndicator(indicator, checked) {
  console.log(indicator.config)
  if (checked) {
    indicatorSelects.value.push(indicator)
    await initIndicatorValues(indicator.id)
    await loadIndicatorValues(indicator.id)
  }else {
    indicatorSelects.value = indicatorSelects.value.filter(i => i.id !== indicator.id)
    chart.value.removeSeries(indicatorValueSeries.value[indicator.id])
    delete indicatorValueSeries.value[indicator.id]
    delete lastFromCandleIndicatorValue.value[indicator.id]
    delete lastToCandleIndicatorValue.value[indicator.id]
  }
  showSecondaryChart.value = indicatorSelects.value.some(i => i.config.secondary)
}

async function toggleTimeframe(timeframe){
  await loadCandles()
  for (const indicator of indicatorSelects.value) {
    await loadIndicatorValues(indicator.id)
  }
}

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

function initChart2() {
  chart2.value = createChart(chartContainer2.value, {
    layout: {
      background: { color: '#ffffff' },
      textColor: '#333',
    },
    width: chartContainer2.value.clientWidth,
    height: 150,
  })

  // 1. crear serie primero
  rsiSeries.value = chart2.value.addLineSeries({
    color: '#2962FF',
    lineWidth: 2,
  })

  // 2. fijar escala RSI (IMPORTANTE usar 'right')
  chart2.value.priceScale('right').applyOptions({
    autoScale: false,
    minValue: 0,
    maxValue: 100,
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


  const candles = res.data.candles
  const formatedCandles = formatCandles(candles)

  candleSeries.value.setData(formatedCandles)

  if(candles.length){
    const last_candle = candles[candles.length - 1]
    historicFirstCandle.value = candles[0].id
    last_candle_id.value = last_candle.id
    last_candle_time.value = last_candle.opened_at
  }

  last_updated.value = new Date(res.data.last_updated * 1000).toLocaleString()

  isLoading.value = false
}

async function loadIndicatorValues(indicator) {
  isLoading.value = true
  let from = historicFirstCandle.value
  let to = last_candle_id.value

  const res = await axios.get(
    route('indicatorValues.get', indicator),
    {
      params: {
        from:from,
        to:to
      }
    }
  )

  const values = res.data.values

  const formatedValues = formatIndicatorValues(values)

  indicatorValueSeries.value[indicator].setData(formatedValues)

  if(values.length){
    lastToCandleIndicatorValue.value[indicator] = values[values.length - 1].candle_id
    lastFromCandleIndicatorValue.value[indicator] = values[0].candle_id
  }

  isLoading.value = false
}


async function initIndicatorValues(indicator) {

  if(!indicatorValueSeries.value[indicator]){
    indicatorValueSeries.value[indicator] =
      chart.value.addLineSeries({
        color: getColor(indicator),
        lineWidth: 2,
      })
  }

}


async function loop() {
  if (isRunning) return;
  isRunning = true
  await runSync();
  isRunning =false
  setTimeout(loop, 1000);
}

async function runSync() {
  try {

    update_time.value = new Date().toLocaleString();

    if (!last_candle_time.value) return;

    // -------------------------
    // 1. CANDLES
    // -------------------------
    const resCandles = await axios.get(
      route('pairs.candles.getLive', props.pair.id),
      {
        params: {
          timeframe: timeframe.value,
        }
      }
    );

    const newCandles = resCandles.data.candles || [];
    const formatedCandles = formatCandles(newCandles);

    if (formatedCandles.length) {
      for (const candle of formatedCandles) {
        if (candle.time >= last_candle_time.value) {
          candleSeries.value.update(candle);

          if (candle.time !== last_candle_time.value) {
            last_candle_time.value = candle.time;
            last_candle_id.value = candle.id;
          }
        }
      }
    }

    // -------------------------
    // 2. INDICATORS
    // -------------------------

    for (const indicator of indicatorSelects.value) {


      if (!indicatorValueSeries.value[indicator.id] || !lastToCandleIndicatorValue.value[indicator.id]) continue;

      const res = await axios.get(
        route('indicatorValues.getLive', indicator.id),
        {
          params: {
            timeframe: timeframe.value,
          }
        }
      );

      const values = res.data.values || [];

      const formatedValues = formatIndicatorValues(values);

      for (const value of formatedValues) {
        if (value.candle_id >= lastToCandleIndicatorValue.value[indicator.id]){
          indicatorValueSeries.value[indicator.id].update(value);
          if(value.candle_id > lastToCandleIndicatorValue.value[indicator.id]){
            lastToCandleIndicatorValue.value[indicator.id] = value.candle_id
          }
        }
      }
    }

  } catch (err) {
    console.error('sync error:', err);
  }
}

const formatCandles = (candles) => {
  return candles.map(c => ({
    id: Number(c.id),
    time: Number(c.opened_at),
    open: Number(c.open),
    high: Number(c.high),
    low: Number(c.low),
    close: Number(c.close),
  }))
}

const formatIndicatorValues = (values) => {
  return values.map(v => ({
    candle_id: Number(v.candle_id),
    time: Number(v.time),
    value: typeof v.value === 'object'
      ? Number(v.value.value)
      : Number(v.value),
  }))
}

const getColor = ((id) => {
  const colors = {
    '1': '#05EF63',
    '2': '#A91F92',
    '3': '#220012',
    '4': '#920F12'
  }
  return colors[id]
})


onMounted(async() => {
  initChart()
  initChart2()
  await loadCandles()
  loop();
})
</script>

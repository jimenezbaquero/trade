<template>
  <Head :title="pair.symbol"/>

  <AppLayout :isLoading="isLoading">

    <div class="px-6">

      <!-- HEADER -->
      <div class="flex items-center mb-4">
        <div class="w-1/2">
          <h1 class="text-2xl font-bold">
            {{ pair.symbol }} - {{ pair.exchange.name }} - {{ current_price? current_price.toFixed(2) : '' }}
            {{ pair.quote_asset }}
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

      <div class="bg-white">
        <!-- CHART -->
        <div
          ref="chartContainer"
          id="chart1"
          class="w-full h-[500px] bg-white rounded-t-lg shadow"
        ></div>
        <!-- CHART2-->
        <div v-show="showSecondaryChart"
             ref="chartContainer2"
             id="chart2"
             class="w-full h-[180px] bg-white rounded-b-lg shadow border-t"
        ></div>
      </div>
    </div>

  </AppLayout>
</template>

<script setup>
import {Head} from '@inertiajs/vue3'
import AppLayout from "@/Layouts/AppLayout.vue"
import BaseSelect from "@/Components/BaseSelect.vue"
import {ref, onMounted, nextTick} from 'vue'
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
const current_price = ref(0)

let isRunning = false;
let resizeObserver = null;

function isSelected(indicator) {
  return indicatorSelects.value.some(i => i.id === indicator.id)
}

async function toggleIndicator(indicator, checked) {
  if(checked){

    if(!indicator.config.main && !chart2.value){

      showSecondaryChart.value = true

      await nextTick()

      initChart2()
      initResizeObserver()
      syncCharts()
    }

    indicatorSelects.value.push(indicator)

    await initIndicatorValues(indicator)
    const visibleRange = chart.value.timeScale().getVisibleRange();
    await loadIndicatorValues(indicator.id)
    chart.value.timeScale().setVisibleRange(visibleRange);
  }else{
    console.log('exists in map:', indicatorValueSeries.value[indicator.id]);

    console.log('chart has series?', chart.value);
    indicatorSelects.value = indicatorSelects.value.filter(i => i.id !== indicator.id)
    if(indicator.config.main){
      chart.value.removeSeries(indicatorValueSeries.value[indicator.id])
    }else{
      chart2.value.removeSeries(indicatorValueSeries.value[indicator.id])
    }
    delete indicatorValueSeries.value[indicator.id]
    delete lastFromCandleIndicatorValue.value[indicator.id]
    delete lastToCandleIndicatorValue.value[indicator.id]
  }
  showSecondaryChart.value = indicatorSelects.value.some(i => !i.config.main)

  if(showSecondaryChart.value){
    chart.value.applyOptions({
      timeScale: {
        visible: false,
      }
    });
  }else{
    chart.value.applyOptions({
      timeScale: {
        visible: true,
      }
    });
  }
}

async function toggleTimeframe(timeframe) {
  await loadCandles()
  for(const indicator of indicatorSelects.value){
    await loadIndicatorValues(indicator.id)
  }
}

function initChart() {

  chart.value = createChart(chartContainer.value, {

    layout: {
      background: {color: '#ffffff'},
      textColor: '#333',
    },

    grid: {
      vertLines: {
        color: '#f0f0f0',
      },
      horzLines: {
        color: '#f0f0f0',
      },
    },

    rightPriceScale: {
      borderColor: '#e5e7eb',
      minimumWidth:'70'
    },

    timeScale: {
      borderColor: '#e5e7eb',
      tickMarkFormatter: (time) => {
        const date = new Date(time * 1000)

        return date.toLocaleString('es-ES', {
          day: '2-digit',
          month: '2-digit',
          hour: '2-digit',
          minute: '2-digit',
        })
      },
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
      background: {color: '#ffffff'},
      textColor: '#333',
    },

    grid: {
      vertLines: {
        color: '#f0f0f0',
      },
      horzLines: {
        color: '#f0f0f0',
      },
    },

    rightPriceScale: {
      borderColor: '#e5e7eb',
      minimumWidth:'70'
    },

    timeScale: {
      visible: true,
      borderColor: '#e5e7eb',
      tickMarkFormatter: (time) => {
        const date = new Date(time * 1000)

        return date.toLocaleString('es-ES', {
          day: '2-digit',
          month: '2-digit',
          hour: '2-digit',
          minute: '2-digit',
        })
      },
    },

    width: chartContainer2.value.clientWidth,
    height: 180,
  })

  syncCharts()
}

function syncCharts() {


  if(!chart.value || !chart2.value) return

  let syncing = false

  chart.value.timeScale().subscribeVisibleLogicalRangeChange((range) => {

    if(syncing || !range) return

    syncing = true
    chart2.value.timeScale().setVisibleLogicalRange(range)
    syncing = false
  })

  chart2.value.timeScale().subscribeVisibleLogicalRangeChange((range) => {

    if(syncing || !range) return

    syncing = true
    chart.value.timeScale().setVisibleLogicalRange(range)
    syncing = false
  })

}

function initResizeObserver() {
  resizeObserver = new ResizeObserver(() => {
    if (chartContainer.value && chart.value) {
      chart.value.applyOptions({
        width: chartContainer.value.getBoundingClientRect().width,
      });
    }

    if (chartContainer2.value && chart2.value) {
      chart2.value.applyOptions({
        width: chartContainer2.value.getBoundingClientRect().width,
      });
    }
  });

  resizeObserver.observe(chartContainer.value);
  resizeObserver.observe(chartContainer2.value);
}

async function loadCandles() {
  isLoading.value = true
  isRunning = true

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
    current_price.value = last_candle.close
  }

  last_updated.value = new Date(res.data.last_updated * 1000).toLocaleString()

  isRunning= false
  isLoading.value = false
}

async function loadIndicatorValues(indicator) {
  isLoading.value = true
  isRunning = true
  let from = historicFirstCandle.value
  let to = last_candle_id.value

  const res = await axios.get(
    route('indicatorValues.get', indicator),
    {
      params: {
        from: from,
        to: to
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
  isRunning = false
  isLoading.value = false
}


async function initIndicatorValues(indicator) {
  console.log(indicator)
  if(!indicatorValueSeries.value[indicator.id]){
    if(indicator.config.main){
      indicatorValueSeries.value[indicator.id] =
        chart.value.addLineSeries({
          color: getColor(indicator.id),
          lineWidth: 2,
        })
    }else{

      const series = chart2.value.addLineSeries({
        color: getColor(indicator.id),
        lineWidth: 2,
      })

      series.createPriceLine({
        price: 70,
        color: '#999',
        lineWidth: 1,
        lineStyle: 2,
        axisLabelVisible: true,
        title: '70',
      })

      series.createPriceLine({
        price: 30,
        color: '#999',
        lineWidth: 1,
        lineStyle: 2,
        axisLabelVisible: true,
        title: '30',
      })

      series.applyOptions({
        autoscaleInfoProvider: () => ({
          priceRange: {
            minValue: 0,
            maxValue: 100,
          },
        }),
      })

      indicatorValueSeries.value[indicator.id] = series
    }
  }

}


async function loop() {
  if(isRunning) return;
  isRunning = true
  await runSync();
  isRunning = false
  setTimeout(loop, 1000);
}

async function runSync() {
  try{

    update_time.value = new Date().toLocaleString();

    if(!last_candle_time.value) return;

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

    if(formatedCandles.length){
      for(const candle of formatedCandles){
        if(candle.time >= last_candle_time.value){
          candleSeries.value.update(candle);
          current_price.value = candle.close

          if(candle.time !== last_candle_time.value){
            last_candle_time.value = candle.time;
            last_candle_id.value = candle.id;
            last_updated.value = new Date(candle.time * 1000).toLocaleString()
          }
        }
      }
    }

    // -------------------------
    // 2. INDICATORS
    // -------------------------

    for(const indicator of indicatorSelects.value){


      if(!indicatorValueSeries.value[indicator.id] || !lastToCandleIndicatorValue.value[indicator.id]) continue;

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

      for(const value of formatedValues){
        if(value.candle_id >= lastToCandleIndicatorValue.value[indicator.id]){
          indicatorValueSeries.value[indicator.id].update(value);
          if(value.candle_id > lastToCandleIndicatorValue.value[indicator.id]){
            lastToCandleIndicatorValue.value[indicator.id] = value.candle_id
          }
        }
      }
    }

  }catch(err){
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
  await loadCandles()
  loop();

})
</script>

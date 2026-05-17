import { createI18n } from 'vue-i18n'

// import en from './lang/en.json'
import es from '../lang/es.json'

const i18n = createI18n({
  legacy: false,
  locale: 'es',
  fallbackLocale: 'en',

  messages: {
    es,
  },
})

export default i18n
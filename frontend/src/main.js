import Vue from 'vue'
import App from './App.vue'
import router from './router'
import './styles/theme.css'


import Notifications from 'vue-notification'
Vue.use(Notifications)

Vue.component('vue-confirm-dialog', {
  name: 'VueConfirmDialogStub',
  template: '<div style="display:none"></div>'
})

Vue.config.productionTip = false

new Vue({ router, render: h => h(App) }).$mount('#app')

import Vue from "vue"
import VueRouter from "vue-router"
Vue.use(VueRouter)

const routes = [
  { path: "/", redirect: "/reports/dashboards" },

  {
    path: "/reports/dashboards",
    name: "reports.dashboards",
    component: () => import("../components/reports/dashboards/Index.vue"),
  },

  { path: "*", redirect: "/reports/dashboards" },
]

export default new VueRouter({ mode: "history", routes })

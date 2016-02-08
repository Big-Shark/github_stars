import Vue from 'vue'
import App from './App'

Vue.filter('chosen', function (repositories, keywords) {

  var activeKeywords = []
  for (var key in keywords) {
    var isActive = keywords[key]['isActive']
    if (isActive) {
      activeKeywords.push(key)
    }
  }

  if (activeKeywords.length === 0) {
    return repositories
  }

  repositories = repositories.filter(function (item) {
    var result = false
    activeKeywords.forEach(function (keyword) {
      if (item['composer']['keywords'].indexOf(keyword) !== -1) {
        result = true
      }
    })
    return result
  })

  return repositories
})

Vue.use(require('vue-resource'))

/* eslint-disable no-new */
new Vue({
  el: 'body',
  components: { App }
})

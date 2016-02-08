<style>
    .hidden {
        display: none;
    }
    .clearfix {
        clear: none;
    }
    a {
        cursor:pointer;
    }
    .activeKeyword {
        color: red;
    }
    .stars {
        float: right;
    }
    .repository {
        margin: 5px;
    }
    .content {
        padding-left: 5px;
        padding-right: 5px;
        padding-bottom: 5px;
        border: 1px darkgray solid;
        border-bottom-left-radius: 5px;
        border-bottom-right-radius: 5px;
    }
    .header {
        padding-left: 5px;
        padding-right: 5px;
        padding-top: 5px;
        background: darkgray;
        border-top-left-radius: 5px;
        border-top-right-radius: 5px;
    }
    .loading {
        cursor:wait;
    }
    .sidebar {
        float: left;
        width: 10%;
        padding-top: 5px;
    }
    .keywords {
        padding: 5px;
        border: 1px gray solid;
        border-radius: 5px;
    }
    .main {
        float: left; width: 90%
    }
</style>

<template>
    <input v-model="userName" value="">
    <button @click="updateRepositories">Update</button> <span v-bind:class="{ 'hidden': loading_current === loading_all}">Loading {{ loading_current }}/{{ loading_all }}</span>

    <div class="clearfix"></div>

    <div class="sidebar" v-bind:class="{ 'loading': loading_current !== loading_all, 'hidden': keywords_length === 0}">
        <div class="keywords">
        <template v-for="keyword in keywords | orderBy 'count' -1">
            <a @click="keywordClick($key)" v-bind:class="{ 'activeKeyword': keyword.isActive }">{{ $key }} [{{ keyword.count }}]</a><br />
        </template>
        </div>
    </div>
    <div class="main" v-bind:class="{ 'loading': loading_current !== loading_all}">
        <div class="repository" v-for="item in repositories | chosen keywords | orderBy 'stargazers_count' -1">
            <div class="header">
                {{ item.full_name }}
                <span class="stars">
                    Stars {{ item.stargazers_count }}
                </span>
            </div>
            <div class="content">
                {{ item.composer.description }}
            </div>
        </div>
    </div>
</template>

<script type="text/ecmascript-6">
export default {

  methods: {
    updateRepositories: function () {
      if (this.userName !== '') {
        this.$set('loading_all', 1)
        this.$set('loading_current', 0)
        this.$set('repositories', [])

        var $this = this
        var loading_current = 0

        this.$http({url: '/repositories/' + this.userName, method: 'GET'}).then(function (response) {
          $this.$set('loading_all', response.data.length)
          response.data.forEach(function (item, i, arr) {
            $this.$http({url: '/composer/' + item['full_name'], method: 'GET'}).then(function (response) {
              loading_current = loading_current + 1
              $this.$set('loading_current', loading_current)
              if (response.data !== null && 'keywords' in response.data) {
                item['composer'] = response.data
                $this.repositories.push(item)
              }
            }, function (response) {
              loading_current = loading_current + 1
              $this.$set('loading_current', loading_current)
            })
          })
        })
      }
    },

    keywordClick: function (keyword) {
      this.keywords[keyword]['isActive'] = !this.keywords[keyword]['isActive']
      this.$set('keywords', this.keywords)
    }
  },

  ready () {
    // GET request
    this.updateRepositories()

    this.$watch('repositories', function (repositories) {
      var $keywords = {}
      repositories.forEach(function (item, i, arr) {
        item['composer']['keywords'].forEach(function (item, i, arr) {
          if (!(item in $keywords)) {
            $keywords[item] = {isActive: false, count: 1}
          } else {
            $keywords[item]['count'] += 1
          }
        })
      })
      this.$set('keywords', $keywords)
    })

    this.$watch('keywords', function (keywords) {
      this.$set('keywords_length', Object.keys(keywords).length)
    })
  },

  data () {
    return {
      repositories: [],
      userName: '',
      keywords: {},
      keywords_length: 0,
      loading_current: 0,
      loading_all: 0
    }
  },

  computed () {
    return {
    }
  }
}
</script>

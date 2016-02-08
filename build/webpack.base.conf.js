var path = require('path')
require('es6-promise').polyfill()

module.exports = {
  entry: {
    app: './src/resources/main.js'
  },
  output: {
    path: path.resolve(__dirname, '../public/assets'),
    publicPath: '/public/',
    filename: '[name].js'
  },
  resolve: {
    extensions: ['', '.js', '.vue'],
    alias: {
      'src': path.resolve(__dirname, '../src/resources')
    }
  },
  resolveLoader: {
    //root: path.join(__dirname, 'node_modules')
  },
  module: {
    loaders: [
      {
        test: /\.vue$/,
        loader: 'vue'
      },
      {
        test: /\.js$/,
        loader: 'babel!eslint',
        exclude: /node_modules/
      },
      {
        test: /\.json$/,
        loader: 'json'
      },
      {
        test: /\.(png|jpg|gif|svg)$/,
        loader: 'url',
        query: {
          limit: 10000,
          name: '[name].[ext]?[hash:7]'
        }
      }
    ]
  },
  vue: {
    loaders: {
      js: 'babel!eslint'
    }
  },
  eslint: {
    formatter: require('eslint-friendly-formatter')
  }
}

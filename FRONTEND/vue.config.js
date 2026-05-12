const { defineConfig } = require('@vue/cli-service')
module.exports = defineConfig({
  transpileDependencies: true,
  lintOnSave: false,
  
  // Configuración para SPA en subdominio
  publicPath: process.env.NODE_ENV === 'production' ? '/' : '/',
  
  // Configuración del servidor de desarrollo
  devServer: {
    historyApiFallback: true
  },
  
  // Configuración de build
  configureWebpack: {
    // Asegurar que las rutas funcionen correctamente
    output: {
      publicPath: '/'
    }
  },

  chainWebpack: config => {
    config.module
      .rule('js')
      .use('babel-loader')
      .tap(options => ({
        ...options,
        cacheDirectory: false
      }))
  }
})

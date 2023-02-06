const path = require('path');

module.exports = {
    module: {
        rules: [
            {
                test: /\.m?js$/,
                exclude: /(node_modules|bower_components)/,
                use: {
                    loader: 'babel-loader',
                    options: {
                        presets: ['@babel/preset-env'],
                        plugins: ['@babel/plugin-proposal-object-rest-spread']
                    }
                }
            }
        ]
    },
    entry: './resources/js/src/index.js',
    output: {
        filename: 'ext.twig.message.js',
        path: path.resolve(__dirname, 'resources/js/dist')
    }
};

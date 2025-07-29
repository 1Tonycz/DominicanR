const path = require('path');

module.exports = {
    entry: {
        master: './scripts/master.js',
        home: './scripts/home.js',
    },
    output: {
        filename: '[name].js',
        path: path.resolve(__dirname, 'www/js'),
    },
    module: {
        rules: [
            // CSS loader pro FilePond a další .css soubory
            {
                test: /\.css$/i,
                use: ['style-loader', 'css-loader'],
            },
            // SCSS loader (volitelný, pokud používáš .scss)
            {
                test: /\.s[ac]ss$/i,
                use: ['style-loader', 'css-loader', 'sass-loader'],
            },
            // Volitelně: loader pro obrázky používané v CSS (např. FilePond preview icons)
            {
                test: /\.(png|jpe?g|gif|svg)$/i,
                type: 'asset',
            },
        ],
    },
    resolve: {
        // Volitelně – usnadní importy bez přípon
        extensions: ['.js', '.json', '.css', '.scss'],
    },
};

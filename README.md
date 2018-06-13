# laravel-admin
This package gives you an admin panel to manage your website's content.
It uses helori/laravel-admin for authentication.
You need to follow the installation steps of this package before using your backend.

## Installation and setup

On a fresh Laravel (>= v5.4) installation, install the package by running:
```bash
composer require helori/laravel-backend
```

Follow helori/laravel-admin installation steps

Publish the laravel-backend default assets:
```bash
php artisan vendor:publish --tag=laravel-backend-assets
```

Install the package's dependencies: 
```bash
npm install jquery@3.* bootstrap@4.* vue-crud@2.* --save-dev
```

Edit your laravel mix config file :
```js
// webpack.mix.js
mix.sass(
    "./resources/assets/sass/backend.scss",
    "./public/css/backend.css"
).js(
    "./resources/assets/js/backend.js",
    "./public/js/backend.js", "."
);
```

Compile your assets :
```bash
npm run dev
```

Your backend should be available at:
```bash
http://your-domain.test/admin
```

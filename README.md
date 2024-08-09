### Installation Instructions

After installing the plugin, navigate to the plugin directory and run the following command to install necessary dependencies:

```
cd plugins/pensoft/restcoastmobileapp
composer install
```

Then you should go back to the root of the project and run:

```
php artisan cache:clear
php artisan config:clear
php artisan config:cache
```

sudo docker-compose up -d
sleep 20
sudo docker-compose exec wcos-backend composer install
docker-compose exec wcos-backend rm -rf app/storage/logs/laravel.logs
docker-compose exec wcos-backend chmod -R 777 storage
docker-compose exec wcos-backend php artisan key:generate
docker-compose exec wcos-backend php artisan db:seed --class=PermissionsTableSeeder

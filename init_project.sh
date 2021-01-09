cp .env.example .env
echo "127.0.0.1 pay_task.local" | sudo tee --append /etc/hosts > /dev/null

cd docker
cp .env.example .env
docker-compose up -d

docker exec -ti pay_task_php bash
composer install
php yii migrate --interactive=0
php yii fixture/load "*" --interactive=0
exit

cd ..
sudo chgrp -R www-data .
cp .env.example .env
echo "127.0.0.1 pay_task.local" | sudo tee --append /etc/hosts > /dev/null

cd docker
cp .env.example .env
docker-compose up -d

docker exec -ti pay_task_php bash
composer install
exit
cd ..

sudo chgrp -R www-data .
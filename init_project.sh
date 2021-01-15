cp .env.example .env
echo "127.0.0.1 pay_task.local" | sudo tee --append /etc/hosts > /dev/null

cd docker
cp .env.example .env
docker-compose up -d

docker exec -ti pay_task_php bash
composer install
php yii migrate --interactive=0
php yii fixture/load "*" --interactive=0

crontab -l > mycron
echo "0 * * * * php yii exec-transfer" >> mycron
crontab mycron
rm mycron

exit

cd ..
sudo chgrp -R www-data .
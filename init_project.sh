cp .env.example .env
echo "127.0.0.1 pay_task.local" | sudo tee --append /etc/hosts > /dev/null

cd docker
cp .env.example .env
docker-compose up -d

docker exec -ti pay_task_php bash
composer install
php yii migrate --interactive=0
php yii migrate --migrationPath=@yii/rbac/migrations/ --interactive=0
php yii rbac/init-roles
php yii fixture/load "*" --interactive=0
php yii rbac/assign-roles-for-fixture-users

crontab -l > mycron
echo "0 * * * * php yii exec-transfer" >> mycron
crontab mycron
rm mycron

exit

cd ..
sudo chgrp -R www-data .
sudo chmod 777 -R runtime
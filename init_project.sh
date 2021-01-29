cp .env.example .env
echo "127.0.0.1 pay_task.local" | sudo tee --append /etc/hosts > /dev/null

cd docker
cp .env.example .env

docker-compose build --no-cache
docker-compose up -d
docker exec pay_task_php composer install
docker exec pay_task_php php yii migrate --interactive=0
docker exec pay_task_php php yii migrate --migrationPath=@yii/rbac/migrations/ --interactive=0
docker exec pay_task_php php yii rbac/init-roles
docker exec pay_task_php php yii fixture/load "*" --interactive=0
docker exec pay_task_php php yii rbac/assign-roles-for-fixture-users
docker exec pay_task_php chgrp -R www-data .
docker exec pay_task_php chmod 777 -R runtime
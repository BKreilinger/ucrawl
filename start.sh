#bin/bash
cd docker;
docker-compose up -d;
docker-compose exec -u laradock workspace bash;
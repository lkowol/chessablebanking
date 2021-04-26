#!/bin/bash

dockerBuild () {
   cd docker && docker-compose build && cd ..
}

runWithoutMutagen () {
   cd docker && docker-compose up &
   cd public/assets/panel && npm install admin-lte && cd ../../..
   sleep 10
   docker exec docker_php-fpm_1 php /var/www/bin/console cache:clear
   docker exec docker_php-fpm_1 php /var/www/bin/console chessable-banking:migration:migrate
}

run () {
    runWithoutMutagen
   mutagen project terminate
   mutagen project start
}

stop () {
   mutagen project terminate
   cd docker && docker-compose stop
   mutagen terminate --all
}

reloadServices () {
  docker exec docker_php-fpm_1 supervisorctl reload
  echo "Services reloaded"
}

case $1 in
  build)
    dockerBuild
  ;;
  run)
    run
  ;;
  runWithoutMutagen)
    runWithoutMutagen
  ;;
  stop)
    stop
  ;;
  reload-services)
      reloadServices
  ;;
esac
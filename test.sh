# Mettre les droits avec : chmod +x test.sh
# puis exécuter avec ./test.sh
bin/console d:f:l --group=AppFixturesTest --env=test -n
bin/phpunit

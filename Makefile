.PHONY: dev test deploy logs

# Lokaler Entwicklungsserver
dev:
	php artisan serve

# Tests ausführen
test:
	php artisan test

# Auf den Heimserver deployen (rsync + docker compose up --build)
deploy:
	bash deploy.sh

# Container-Logs auf dem Heimserver ansehen
logs:
	ssh heimserver "cd /mnt/piStorage/docker/road-to-strong && docker compose logs -f --tail=100"

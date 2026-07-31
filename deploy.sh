#!/bin/bash
set -e

REMOTE="heimserver"
REMOTE_DIR="/mnt/piStorage/docker/road-to-strong"

echo "==> Syncing files to $REMOTE:$REMOTE_DIR ..."
ssh "$REMOTE" "mkdir -p $REMOTE_DIR/data"
rsync -avz --delete \
  --exclude='node_modules' --exclude='vendor' --exclude='.git' \
  --exclude='.env' --exclude='.env.*' \
  --exclude='database/database.sqlite*' \
  --exclude='public/build' --exclude='storage/logs' \
  --exclude='data' \
  "$(dirname "$0")/" "$REMOTE:$REMOTE_DIR/"

# .env nur beim Erstdeploy kopieren — danach ist der Server die Quelle
if ! ssh "$REMOTE" "test -f $REMOTE_DIR/.env"; then
  echo "==> Erstdeploy: kopiere .env.production als .env ..."
  scp "$(dirname "$0")/.env.production" "$REMOTE:$REMOTE_DIR/.env"
fi

echo "==> Building and starting containers ..."
ssh "$REMOTE" "cd $REMOTE_DIR && docker compose up -d --build"

echo ""
echo "==> Done! Road to Strong läuft auf http://100.102.83.46:3008 (nur Tailscale)"

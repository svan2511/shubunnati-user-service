#!/bin/sh
set -e

# Substitute env vars into Nginx config
envsubst '${PORT}' < /etc/nginx/sites-available/default.template > /etc/nginx/sites-available/default

# Run the original CMD (Supervisor)
exec "$@"
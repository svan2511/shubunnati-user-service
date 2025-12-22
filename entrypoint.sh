#!/bin/sh
set -e

echo "Using PORT from Railway: $PORT"

# Substitute ${PORT} in the Nginx template
envsubst '${PORT}' < /etc/nginx/sites-available/default.template > /etc/nginx/sites-available/default

# Verify the generated config (for debugging)
echo "Generated Nginx config:"
cat /etc/nginx/sites-available/default

# Test Nginx config syntax
nginx -t

# Start Supervisor (which starts PHP-FPM and Nginx)
exec "$@"
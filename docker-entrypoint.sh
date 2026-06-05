#!/bin/bash
set -e

echo "Starting entrypoint script..."

# --- Fix for Apache MPM conflicts ---
# Disable all possible MPM modules, suppressing errors if they are already disabled.
echo "Disabling conflicting MPM modules..."
a2dismod mpm_event 2>/dev/null || true
a2dismod mpm_worker 2>/dev/null || true
a2dismod mpm_prefork 2>/dev/null || true

# Enable the correct module (mpm_prefork) for PHP.
echo "Enabling mpm_prefork module..."
a2enmod mpm_prefork

# --- Your other runtime commands ---
# Example: Set the correct DocumentRoot if needed.
# sed -i 's#/var/www/html#/var/www/public#' /etc/apache2/sites-available/000-default.conf

PORT=${PORT:-8080}
echo "Setting Apache to listen on port ${PORT}..."
sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf
echo "ServerName localhost:${PORT}" >> /etc/apache2/apache2.conf

# --- Start Apache ---
echo "Starting Apache..."
exec apache2-foreground
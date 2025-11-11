#!/bin/sh

if [ ! -f /etc/apache2/ssl/localhost.crt ]; then
  echo "Generando certificado SSL..."
  openssl req -x509 -newkey rsa:2048 -nodes \
    -keyout /etc/apache2/ssl/localhost.key \
    -out /etc/apache2/ssl/localhost.crt \
    -days 365 \
    -subj "/CN=localhost"
else
  echo "Certificado SSL ya existe -> saltando generación."
fi

apache2-foreground

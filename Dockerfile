# Dockerfile
FROM areesmoon/wppconnect-server

# Set environment for wppconnect-server
ENV NODE_ENV=production PUPPETEER_SKIP_CHROMIUM_DOWNLOAD=true

# Install apache, php & php-curl
RUN apk add --no-cache apache2 php82-apache2 php82-curl && rm -rf /var/cache/apk/*

# Apache configuration
RUN echo "ServerName localhost" >> /etc/apache2/httpd.conf && \
    echo "LoadModule rewrite_module modules/mod_rewrite.so" >> /etc/apache2/httpd.conf && \
    echo "<Directory /var/www/localhost/htdocs/>" >> /etc/apache2/httpd.conf && \
    echo "  Options Indexes FollowSymLinks" >> /etc/apache2/httpd.conf && \
    echo "  AllowOverride All" >> /etc/apache2/httpd.conf && \
    echo "  Require all granted" >> /etc/apache2/httpd.conf && \
    echo "</Directory>" >> /etc/apache2/httpd.conf

# Copy web files
COPY --chown=www-data:www-data html/ /var/www/localhost/htdocs/
RUN rm -f /var/www/localhost/htdocs/index.html
WORKDIR /root/wppconnect-server

EXPOSE 22
EXPOSE 21465
EXPOSE 80

# Run sshd & httpd
# Check WPPConnect update
# Run WPPConnect-server
ENTRYPOINT /usr/sbin/sshd -e "$@" && \
  httpd -D BACKGROUND && \
  echo "Checking WPPConnect update..." && \
  if [ "$(npm outdated | grep '@wppconnect-team/wppconnect' | awk '{print $1}')" = "@wppconnect-team/wppconnect" ]; \
  then \
    echo "Updating WPPConnect..." && npm update @wppconnect-team/wppconnect; \
  else \
    echo "WPPConnect is up to date"; \
  fi && \
  yarn dev
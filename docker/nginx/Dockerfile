ARG NGINX_VERSION=1.15

### NGINX
FROM nginx:${NGINX_VERSION}-alpine AS symfony_docker_nginx

COPY conf.d /etc/nginx/conf.d/

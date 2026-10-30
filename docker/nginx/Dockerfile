ARG VERSION

FROM nginx:${VERSION}-alpine as dev

RUN apk add --no-cache openssl && \
    mkdir -p /etc/nginx/ssl && \
    openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
        -keyout /etc/nginx/ssl/nginx.key \
        -out /etc/nginx/ssl/nginx.crt \
        -subj "/C=UA/ST=Kyiv/L=Kyiv/O=Development/OU=IT/CN=localhost"

COPY ./docker/nginx/default.conf /etc/nginx/conf.d/default.conf

FROM dev as prod

COPY ./assets /app/public

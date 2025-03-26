FROM mysql:8.0
COPY 00-init-db.sql /docker-entrypoint-initdb.d
COPY 01-init-data.sql /docker-entrypoint-initdb.d
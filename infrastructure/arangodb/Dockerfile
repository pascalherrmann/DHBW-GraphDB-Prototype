FROM arangodb

MAINTAINER Hendrik Pommerening

#COPY data /data

COPY import.sh .

RUN chmod o+rx /root && \
    chmod +x import.sh

RUN ./import.sh


# Anleitung:
# dieses Image bauen mit $ docker build -t arangotest .
# Anschließend wird aus diesem Dockerfile ein Image, basierend auf dem offiziellem ArangoDB-Image (d.h. ArangoDB ist bereits installiert) gebaut.
# In dieses wird der Inhalt des data-Ordners kopiert und anschließend das Script import.sh ausgeführt.
# Anschließend kann ein Container des Images gestartet werden: $ docker run -e ARANGO_RANDOM_ROOT_PASSWORD=1 -d arangotest

#RUN arangorestore --input-directory "dump" --server.password ""

RUN if [ -d /var/lib/arangodb3/databases/database-1/sdf ];    then  echo "SUCCESS!1"; else     echo "neee"; fi
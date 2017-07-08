FROM neo4j

MAINTAINER Pascal Herrmann

WORKDIR /

ARG wikilanguage

COPY import.sh .

RUN chmod o+rx /root && \ 
    chmod +x import.sh   

RUN ./import.sh $wikilanguage
        
#COPY simple-graph-data /data/databases/wikipediadbms

WORKDIR /var/lib/neo4j
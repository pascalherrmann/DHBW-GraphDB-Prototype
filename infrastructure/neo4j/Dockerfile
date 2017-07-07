###### Image basiert auf PHP + Apache-Server ######
FROM neo4j

MAINTAINER Pascal Herrmann

WORKDIR /

COPY import.sh .

RUN chmod o+rx /root && \ 
    chmod +x import.sh   

RUN ./import.sh
        
#COPY simple-graph-data /data/databases/wikipediadbms

WORKDIR /var/lib/neo4j
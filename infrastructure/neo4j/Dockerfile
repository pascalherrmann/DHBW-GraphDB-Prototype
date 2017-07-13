FROM neo4j

MAINTAINER Pascal Herrmann

WORKDIR /

ARG wikilanguage

COPY import.sh .

RUN chmod o+rx /root && \ 
    chmod +x import.sh   

RUN ./import.sh $wikilanguage





COPY prepare.sh .

RUN chmod o+rx /root && \ 
    chmod +x prepare.sh   

RUN ./prepare.sh




        
#COPY simple-graph-data /data/databases/wikipediadbms

WORKDIR /var/lib/neo4j
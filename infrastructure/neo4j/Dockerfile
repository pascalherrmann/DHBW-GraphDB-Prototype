###### Image basiert auf PHP + Apache-Server ######
FROM neo4j:3.2.1

COPY import.sh .

RUN apt-get update && \
    apt-get install -y git maven2

RUN chmod o+rx /root && \ 
    chmod +x import.sh   

RUN ./import.sh
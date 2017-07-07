# Image basiert auf Node.js (d.h. Node.js ist bereits installiert)
FROM node

# App-Verzeichnis erstellen und als Work-Directory festlegen
RUN mkdir -p /app
WORKDIR /app

# Abhängigkeiten installieren (vor Source kopieren, damit im Cache bei Sourcecode Änderungen!)
COPY nodejs/package.json /app/
RUN npm install

# Source-Code kopieren
COPY public /public
COPY nodejs /app



# Port freigeben
EXPOSE 8080
CMD [ "npm", "start" ]
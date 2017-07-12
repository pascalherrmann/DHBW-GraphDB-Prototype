bigEcho () {
   echo "=================================================="
   echo $1
   echo "=================================================="
}

# Changing data directory of Arangodb, since it uses a volume declared in the Dockerfile of library/arangodb
# However, we want to add data to it, but volumes get cleared after every Dockerfile instruction
# So let's change the db directory to a non-volume directory
bigEcho "changing arangodb data directory"
mkdir /arango-data
chmod 777 /arango-data
sed -i.bak 's/^\(directory = \).*/\1\/arango-data/' /etc/arangodb3/arangod.conf

bigEcho "Hallo und Herzlich Willkommen zum Wikipedia-Import bei ARRANGODB!"

echo "Starte arangodb"
arangod &

echo "Installation wget und unzip"
apt-get update
apt-get install -y wget unzip

echo "Starte Download des Dumps"
mkdir dump
cd dump
wget "https://www.dropbox.com/s/hsaew8sf1soyz2q/dump-bar.zip"

unzip dump-bar.zip

cd ..

echo "Starte import des Dumps"
arangorestore --input-directory "dump"
bigEcho "Fertig :)"

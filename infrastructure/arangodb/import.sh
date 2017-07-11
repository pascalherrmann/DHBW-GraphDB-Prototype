bigEcho () {
   echo "=================================================="
   echo $1
   echo "=================================================="
}

bigEcho "Hallo und Herzlich Willkommen zum Wikipedia-Import bei ARRANGODB!"

echo "Starte arangodb"
arangod &

echo "Installation wget und unzip"
apt-get update -q
apt-get upgrade -q
apt-get install wget -q -y
apt-get install unzip -q -y

echo "Starte Download des Dumps"
mkdir dump
cd dump
wget "https://www.dropbox.com/s/hsaew8sf1soyz2q/dump-bar.zip"


unzip dump-bar.zip

cd ..

echo "Starte import des Dumps"
arangorestore --input-directory "dump"
bigEcho "Fertig :)"

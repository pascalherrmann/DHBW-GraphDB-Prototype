#
# Kommandos
#

#### Helping Functions ####
bigEcho () {
   echo "=================================================="
   echo $1
   echo "=================================================="
}

bigEcho "Hallo und Herzlich Willkommen zum Wikipedia-Import!"

echo "Wir beginnen mit der Installation von Maven, git und Java."

# Neo4j Image basiert auf Alpine, deshalb verwenden wir apk.

apk add --no-cache  git maven openjdk8

bigEcho "Okay, cool, geschafft! Als nächstes müssen wir unsere APK-Zertifikate aktualisieren um den Befehl wget verwenden zu können"

# hat ohne nicht geklappt
apk update
apk add ca-certificates wget
update-ca-certificates

bigEcho "Supi. Jetzt erstmal das GRAPHIPEDIA Repository klonen."
git clone git://github.com/mirkonasato/graphipedia.git
cd graphipedia/

bigEcho "Und als nächstes Maven starten!"
mvn install

bigEcho "Nun laden wir den Wikipedia-Dump herunter"
wget http://dumps.wikimedia.org/barwiki/latest/barwiki-latest-pages-articles.xml.bz2

bigEcho "Und entpacken ihn nun"
bzip2 -d barwiki-latest-pages-articles.xml.bz2

bigEcho "Jetzt wird das Skript gestartet:"
echo "Erst parsen der Links"
java -classpath ./graphipedia-dataimport/target/graphipedia-dataimport.jar org.graphipedia.dataimport.ExtractLinks barwiki-latest-pages-articles.xml final-barwiki-links.xml

echo "Dann erstellen der Neo4j-Datenbank"
java -Xmx3G -classpath ./graphipedia-dataimport/target/graphipedia-dataimport.jar org.graphipedia.dataimport.neo4j.ImportGraph final-barwiki-links.xml wikipediadb

bigEcho "Daten-Ordner löschen und neu erstellen, damit man Dinge hineinkopieren kann..."
rm -r /var/lib/neo4j/data
mkdir /var/lib/neo4j/data
mkdir /var/lib/neo4j/data/databases

cp -r wikipediadb /var/lib/neo4j/data/databases/wikipediadbms

echo "Anpassen der Neo4j-Config-Datei"
echo "dbms.allow_format_migration=true" >> /var/lib/neo4j/conf/neo4j.conf
echo "dbms.active_database=wikipediadbms"  >> /var/lib/neo4j/conf/neo4j.conf

if [ -d /var/lib/neo4j/data/databases/wikipediadbms ];
    then  bigEcho "SUCCESS!";
fi

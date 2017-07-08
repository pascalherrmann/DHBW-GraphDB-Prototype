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

if [ -z ${1+x} ]; #wäre auch über Umgebungsvariable $wikilanguage erreichbar!
    then echo "No Language selected! We will download the Bavarian Wiki 🍻";  language="bar";
    else echo "We will download the following wiki: '$1'"; language=$1;
fi

sleep 5

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
wget http://dumps.wikimedia.org/${language}wiki/latest/${language}wiki-latest-pages-articles.xml.bz2

bigEcho "Und entpacken ihn nun"
bzip2 -d ${language}wiki-latest-pages-articles.xml.bz2

bigEcho "Jetzt wird das Skript gestartet:"
echo "Erst parsen der Links"
java -classpath ./graphipedia-dataimport/target/graphipedia-dataimport.jar org.graphipedia.dataimport.ExtractLinks ${language}wiki-latest-pages-articles.xml final-${language}wiki-links.xml

echo "Dann erstellen der Neo4j-Datenbank"
java -Xmx3G -classpath ./graphipedia-dataimport/target/graphipedia-dataimport.jar org.graphipedia.dataimport.neo4j.ImportGraph final-${language}wiki-links.xml neo-wiki-${language}

bigEcho "Daten-Ordner löschen und neu erstellen, damit man Dinge hineinkopieren kann..."
rm -r /var/lib/neo4j/data
mkdir /var/lib/neo4j/data
mkdir /var/lib/neo4j/data/databases

cp -r neo-wiki-${language} /var/lib/neo4j/data/databases/neo-wiki-${language}

echo "Anpassen der Neo4j-Config-Datei"
echo "dbms.allow_format_migration=true" >> /var/lib/neo4j/conf/neo4j.conf
echo "dbms.active_database=neo-wiki-${language}"  >> /var/lib/neo4j/conf/neo4j.conf

if [ -d /var/lib/neo4j/data/databases/neo-wiki-${language} ];
    then  bigEcho "SUCCESS!";
fi

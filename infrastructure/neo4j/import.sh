#
# Kommandos
#

#### Helping Functions ####
bigEcho () {
   echo "=================================================="
   echo $1
   echo "=================================================="
}

start=$(date +%s)

bigEcho "Hallo und Herzlich Willkommen zum Wikipedia-Import!"

if [ -z ${1+x} ]; #wäre auch über Umgebungsvariable $wikilanguage erreichbar!
    then echo "No Language selected! We will download the Bavarian Wiki 🍻";  language="bar";
    else echo "We will download the following wiki: '$1'"; language=$1;
fi

echo -ne '#####                     (2)\r'
sleep 1
echo -ne '#############             (1)\r'
sleep 1
echo -ne '#######################   (0)\r'
sleep 1
echo -ne '\n'

echo "Wir beginnen mit der Installation von Maven, git und Java."

# Neo4j Image basiert auf Alpine, deshalb verwenden wir apk.

apk add --no-cache  git maven openjdk8

bigEcho "Okay, cool, geschafft! Als nächstes müssen wir unsere APK-Zertifikate aktualisieren um den Befehl wget verwenden zu können"

# hat ohne nicht geklappt
apk update
apk add ca-certificates wget
update-ca-certificates

bigEcho "Clonen Graphipedia Repo"
git clone git://github.com/mirkonasato/graphipedia.git
cd graphipedia/

bigEcho "Installing Graphipedia Dependencies with Maven"
mvn install

bigEcho "Downloading Wikipedia-Dump for Language '${language}'"
echo http://dumps.wikimedia.org/${language}wiki/latest/${language}wiki-latest-pages-articles.xml.bz2
wget http://dumps.wikimedia.org/${language}wiki/latest/${language}wiki-latest-pages-articles.xml.bz2

bigEcho "Extracting"
bzip2 -d ${language}wiki-latest-pages-articles.xml.bz2

bigEcho "Starting Graphipedia Service"
echo "1) Parsing links from downloaded XML-File"
java -classpath ./graphipedia-dataimport/target/graphipedia-dataimport.jar org.graphipedia.dataimport.ExtractLinks ${language}wiki-latest-pages-articles.xml final-${language}wiki-links.xml

echo "2) Creating Neo4j Data directory"
java -Xmx3G -classpath ./graphipedia-dataimport/target/graphipedia-dataimport.jar org.graphipedia.dataimport.neo4j.ImportGraph final-${language}wiki-links.xml neo-wiki-${language}

bigEcho "Let's delete the /var/lib/neo4j/data-directory  and create it newly in order to copy or folder into it."
rm -r /var/lib/neo4j/data
mkdir /var/lib/neo4j/data
mkdir /var/lib/neo4j/data/databases

cp -r neo-wiki-${language} /var/lib/neo4j/data/databases/neo-wiki-${language}

echo "Customize Neo4j-Configuration"
echo "dbms.allow_format_migration=true" >> /var/lib/neo4j/conf/neo4j.conf
echo "dbms.active_database=neo-wiki-${language}"  >> /var/lib/neo4j/conf/neo4j.conf

if [ -d /var/lib/neo4j/data/databases/neo-wiki-${language} ];
    then  bigEcho "SUCCESS!";
fi

end=$(date +%s)
duration=$((end-start))

echo "$((duration / 60)) minutes and $((duration % 60)) seconds elapsed."

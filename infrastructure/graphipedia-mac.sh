bigEcho () {
   echo "=================================================="
   echo $1
   echo "=================================================="
}

bigEcho "Herzlich Willkommen zur Wikipedia-Installation 😺"

if [ -z ${1+x} ];
    then echo "No Language selected! We will download the Bavarian Wiki 🍻";  language="bar";
    else echo "We will download the following wiki: '$1'"; language=$1;
fi

bigEcho "Important: You will need git, java, maven for the installation!"
sleep 3

echo "Downloading graphipedia"
git clone git://github.com/mirkonasato/graphipedia.git
cd graphipedia/

bigEcho "Starting Maven!"
mvn install

bigEcho "Downloading Wikipedia Dump"
echo "http://dumps.wikimedia.org/${language}wiki/latest/${language}wiki-latest-pages-articles.xml.bz2"
curl -L -O "http://dumps.wikimedia.org/${language}wiki/latest/${language}wiki-latest-pages-articles.xml.bz2"

bigEcho "Und entpacken ihn nun"
bzip2 -d ${language}wiki-latest-pages-articles.xml.bz2

bigEcho "Jetzt wird das Skript gestartet:"
echo "Erst parsen der Links"
java -classpath ./graphipedia-dataimport/target/graphipedia-dataimport.jar org.graphipedia.dataimport.ExtractLinks ${language}wiki-latest-pages-articles.xml final-${language}wiki-links.xml

echo "Dann erstellen der Neo4j-Datenbank"
java -Xmx3G -classpath ./graphipedia-dataimport/target/graphipedia-dataimport.jar org.graphipedia.dataimport.neo4j.ImportGraph final-${language}wiki-links.xml wikipedia-${language}-db

if [ -d wikipedia-${language}-db ];
    then  bigEcho "Done! 🙈";
fi

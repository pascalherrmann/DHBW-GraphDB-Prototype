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

echo "Important: You will need git, java and maven for the installation!"
sleep 5

echo "First, let's clone the graphipedia-repository."
git clone git://github.com/mirkonasato/graphipedia.git
cd graphipedia/

bigEcho "Now, let's install its dependencies with Maven."
mvn install

bigEcho "Alright. Now we'll download the wikipedia dump for language '${language}' from:"
echo "http://dumps.wikimedia.org/${language}wiki/latest/${language}wiki-latest-pages-articles.xml.bz2"
curl -L -O "http://dumps.wikimedia.org/${language}wiki/latest/${language}wiki-latest-pages-articles.xml.bz2"

bigEcho "Awesome. Now, let's extract it."
bzip2 -d ${language}wiki-latest-pages-articles.xml.bz2

bigEcho "OK, or preparations are done. Now we will start Graphipedia. First, to extract all the links from the downloaded XML-file.:"
java -classpath ./graphipedia-dataimport/target/graphipedia-dataimport.jar org.graphipedia.dataimport.ExtractLinks ${language}wiki-latest-pages-articles.xml final-${language}wiki-links.xml

echo "Dann erstellen der Neo4j-Datenbank"
java -Xmx3G -classpath ./graphipedia-dataimport/target/graphipedia-dataimport.jar org.graphipedia.dataimport.neo4j.ImportGraph final-${language}wiki-links.xml wikipedia-${language}-db

if [ -d wikipedia-${language}-db ];
    then  bigEcho "Done! 🙈";
fi

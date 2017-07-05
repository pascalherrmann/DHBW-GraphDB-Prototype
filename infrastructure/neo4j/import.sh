# Herunterladen von Graphipedia
git clone git://github.com/mirkonasato/graphipedia.git
cd graphipedia

# Maven Dependencies
mvn install

# Wikipedia-Dump herunterladen
wget http://dumps.wikimedia.org/simplewiki/latest/simplewiki-latest-pages-articles.xml.bz2
bzip2 -dk simplewiki-latest-pages-articles.xml.bz2

# Parsen der Seiten
java -classpath ./graphipedia-dataimport/target/graphipedia-dataimport.jar org.graphipedia.dataimport.ExtractLinks simple-latest-pages-articles.xml final-simple-links.xml

# Erstellen der Neo4j - Daten
java -Xmx3G -classpath ./graphipedia-dataimport/target/graphipedia-dataimport.jar org.graphipedia.dataimport.neo4j.ImportGraph final-simple-links.xml simple-graph-data

# Kopieren der Daten
cp simple-graph-data /var/lib/neo4j/data/graph.db

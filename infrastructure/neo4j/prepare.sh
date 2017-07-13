cd /var/lib/neo4j/bin

./neo4j start

echo "Waiting for neo4j to be ready!"
until $(wget -q "http://localhost:7474" -O /dev/null); do
    echo '.'
    sleep 1
done
echo "Awesome! Neo is ready!"

echo "Now lets change our neo password......"
echo "CALL dbms.changePassword('neo');" >  query.cypher
./cypher-shell -u neo4j -p neo4j <  query.cypher

echo "OK. Next, we set every node's count to zero."
echo 'MATCH (some:Page)
      SET some.count = 0;' >  query.cypher

./cypher-shell -u neo4j -p neo <  query.cypher

echo "Finally lets count the incoming nodes."
echo 'START n=node(*)
MATCH (n:Page)<-[r:Link]-(x:Page)
WITH n, COUNT(r) AS incoming
SET n.count = incoming;' > query.cypher

./cypher-shell -u neo4j -p neo <  query.cypher

echo "Done!"

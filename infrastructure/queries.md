# Allgemein
##### Neo-Interface:
* 3 Bereiche:
  * Editor
  * Stream
  * Sidebar
  
##### Cypher
```
# Knoten: ()

# Kante: ()-[Type]->()

# Bezeichner: (pascal) sodass z.B. RETURN pascal.age

# Label: (pascal:Person)
```

##### Knoten erstellen
```
CREATE (some:Person { name: "Pascal", from: "Munich"})
```

##### Mehrere auf einmal erstellen
```
CREATE (some:Person { name: "Hendrik", from: "Hamburg", age: 22}), (some2:Person { name: "Patrick", from: "Frankfurt", age: 21}), (some3:Person { name: "Martin", from: "Munich", age: 25}), (some3)-[:KNOWS {since: 2001}]->(some2)
```

##### Knoten finden
Filtern direkt in Match:
```
MATCH (some:Person{name:"Pascal"}) 
RETURN some.name;
```

Oder komplexeres Eingrenzen der Ergebnisse mit WHERE
```
MATCH (some:Person) 
WHERE some.age > 21  
AND some.age < 23 RETURN some
```


##### Bearbeiten
```
MATCH (n:Person {name:"Pascal"})
SET n :newLabel
SET n.age=21
RETURN n
```

##### Beziehung erstellen
```
MATCH (some1:Person) WHERE some1.name = "Hendrik"
MATCH (some2:Person) WHERE some2.name = "Martin"
CREATE (some1)-[:KNOWS {since: 2001}]->(some2);
```

##### Knoten löschen
```
MATCH (some: Person  { name: 'Pascal' })
DETACH DELETE some
```




# Queries
##### Schema anzeigen
```
CALL db.schema()
```

##### Query beschreiben
```
MATCH (js:Person)-[:KNOWS]-()-[:KNOWS]-(surfer)
WHERE js.name = "Johan" AND surfer.hobby = "surfing"
RETURN DISTINCT surfer
```

#### Mehrere Beziehungen
```
MATCH (tom:Person {name:"Tom Hanks"})-[:ACTED_IN]->(m)<-[:ACTED_IN]-(coActors) RETURN coActors.name
```




# Wikipedia
##### Einführung
* viele interessante Anwendungsfälle - Problem: geeignete, frei verfügbare Daten
* außerdem: möglichst hohe Datenmenge
* Wikipedia-Nutzer kennen das Phänomen
* 6 Degrees of Separation

##### Anzahl der Knoten
```
MATCH (some:Page) 
RETURN count(some)
```

##### Eingehende oder Ausgehende Links einer Seite
```
MATCH (p0:Page {title:'Stuggart'}) -[Link]-> (p:Page)
RETURN p0, p
```

##### Meiste In/Out
```
MATCH (a)
RETURN id(a), a.title, 
size((a)-->()) as out, size((a)<--()) as in
ORDER BY out DESC 
LIMIT 10
```

##### Shortest Path zwischen 2 Seiten
```
MATCH (p0:Page {title:'Takeo Ischi'}), (p1:Page {title:'Datnbank'}),
  p = shortestPath((p0)-[*..71]->(p1))
RETURN p
```

##### Auto-Complete
```
START n = node(*) 
WHERE n.title =~ '.*A.*'
RETURN n.title, n.count
ORDER BY n.count DESC
LIMIT 10
```

# Daten
```
MATCH (ee:Person) WHERE ee.name = "Emil"
CREATE (js:Person { name: "Johan", from: "Sweden", learn: "surfing" }),
(ir:Person { name: "Ian", from: "England", title: "author" }),
(rvb:Person { name: "Rik", from: "Belgium", pet: "Orval" }),
(ally:Person { name: "Allison", from: "California", hobby: "surfing" }),
(ee)-[:KNOWS {since: 2001}]->(js),(ee)-[:KNOWS {rating: 5}]->(ir),
(js)-[:KNOWS]->(ir),(js)-[:KNOWS]->(rvb),
(ir)-[:KNOWS]->(js),(ir)-[:KNOWS]->(ally),
(rvb)-[:KNOWS]->(ally)
```
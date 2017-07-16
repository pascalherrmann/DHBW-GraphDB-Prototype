#!/usr/bin/env python
# coding: utf8
"""XML-Parser for graphipedia to arangoimp"""

# Module für Multi-Core-Support
from multiprocessing import JoinableQueue, Process
## Modul zum Parsen von XML (als C-Implemtierung -> Performance)
import xml.etree.cElementTree as etree
## Module für Systemfunktionen, JSON-Konvetierung, Hashing, etc
import time, hashlib, sys, json, os
## Modul zum Starten von Subprocessen -> arangoimp
from subprocess import Popen, PIPE, STDOUT
## Modul zur Verbindung mit ArangoDB
from arango import ArangoClient

__author__ = "Hendrik Pommerening"
__version__ = "1.0.0"
__maintainer__ = "Hendrik Pommerening"
__email__ = "hendrik_p@me.com"
__status__ = "Production"

## Methode für große Konsolenausgabe definieren
def printBig(str):
    print "###################################################################"
    print str
    print "###################################################################"

## Begrüßung
printBig("ArangoDB Wikipedia XML -> arangoimp ")

## Sprachauswahl + Dateiprüfung
#lang = raw_input("Bitte ein Sprachschluessel eingeben (de = deutsch, bar = bayrisch,...) --> ")
lang = sys.argv[1]
link_coll_name = "links-" + lang
pages_coll_name = "pages"
xmlfile_name = "final-" + lang + "-links.xml"
if not os.path.isfile(xmlfile_name):
    print "Bitte stelle sicher, dass", xmlfile, "vorhanden und lesbar ist!"
    print "Graphipedia für Neo4J erstellt die Datei!"
    print "Mehr Infos unter: https://github.com/mirkonasato/graphipedia"
    sys.exit("Breche ab...")

## Löschen eventuell vorhandener Collections + Anlage
client = ArangoClient()
try:
    db = client.db('_system')
    db.delete_collection(link_coll_name, ignore_missing=True)
    db.create_collection(link_coll_name, edge=True)
    db.delete_collection(pages_coll_name, ignore_missing=True)
    db.create_collection(pages_coll_name)
    link_coll = db.collection(link_coll_name)
    link_coll.configure(journal_size=33554432)
except:
    print "Es konnte keine Verbindung zu ArangoDB hergestellt werden!"
    print "Prüfe, ob ArangoDB unter root@127.0.0.1:8529 erreichbar ist!"
    sys.exit("Breche ab...")



batchsize = int(raw_input("Wie viele Dokumente sollen in einer Anfrage an ArangoDB gesendet werden? (Beispielsweise 1000) --> "))

class Renderer:
    queue = None

    def __init__(self, arg):
        self.collectionname = arg
        self.queue = JoinableQueue()
        self.processes = [Process(target=self.upload) for i in range(4)]
        for p in self.processes:
            p.start()


    def render(self, item):
        self.queue.put(item)

    def upload(self):
        while True:
            item = self.queue.get()
            if item is None:
                break
            # Mit einer batch-size von 2500000 dokument sind es 41500037 Byte die benoetogt werden
            # Fehler war file to big
            batchbytes = batchsize * 175
            arangoimp = 'arangoimp --file - --collection "' + self.collectionname + '" --type json --server.password "" --batch-size ' + str(batchbytes)
            p = Popen((arangoimp),shell=True, stdout=PIPE, stdin=PIPE, stderr=STDOUT)
            p.communicate(input=json.dumps(item))[0]
            self.queue.task_done()

    def terminate(self):
        """ wait until queue is empty and terminate processes """
        self.queue.join()
        for p in self.processes:
            p.terminate()


r = Renderer(collectionname)

print 'Starte mit der XML-Verarbeitung!'
startzeit = time.time()

seiten =[]
count = 0
batch = []

for event, elem in etree.iterparse(xmlfile, events=('start', 'end', 'start-ns', 'end-ns')):
    for child in elem:
        if not isinstance(child.text, basestring):
            continue
        if child.tag == 't':
            hash_from = "pages/" + hashlib.sha256(child.text.encode(encoding="utf-8")).hexdigest()
            #writer.writerow({'_key': hashobj.hexdigest(), 'name': child.text})
            seitentitel = child.text.encode(encoding="utf-8")
            seiten.append(seitentitel)
        if child.tag == 'l':

            hash_to = "pages/" + hashlib.sha256(child.text.encode(encoding="utf-8")).hexdigest()
            #batch.collection(collectionname).insert({'_to': hash_to , '_from': hash_from})
            doc = {'_to': hash_to , '_from': hash_from}
            batch.append(doc)
            count += 1
            if count % batchsize == 0:
                r.render(batch)
                batch = []
            if count % 10000 == 0:
                sys.stdout.write('.')
                sys.stdout.flush()
            if count % 500000 == 0:
                print "Sent", count/1000 , "k // Actual", (coll.count()/1000), "k // Gap: ",  (count-coll.count())/1000, "k"

    elem.clear()

## letzen Schub verarbeiten
r.render(batch)
## auf beendigung der letzen Prozesse warten
print "\n Auf Fertigstellung durch arangoimp warten..."
r.terminate()
exec_time = (time.time() - startzeit)
print "Es wurden ", count, " Links in",exec_time / 60, "Minuten verarbeitet!"
print "Das sind pro Sekunde", count / exec_time, "Links :)"

printBig ("Beginne mit Import der einzelnen Seiten")
startzeit = time.time()


r2 = Renderer(target_coll)
count = 0
batch = []
for page in seiten:
        hash_name = hashlib.sha256(page).hexdigest()
        doc = {'_key': hash_name, 'name': page}
        batch.append(doc)
        count +=1
        if count % batchsize == 0:
            r2.render(batch)
            batch = []
        if count % 1000 == 0:
            sys.stdout.write('.')
            sys.stdout.flush()
        if count % 100000 == 0:
            print "Bereits ", count, " Seiten an arangoimp gesendet!"
## letzen Schub verarbeiten
r2.render(batch)
## auf beendigung der letzen Prozesse warten
print "\n Auf Fertigstellung durch arangoimp warten..."
r2.terminate()
exec_time = (time.time() - startzeit)
print "Es wurden ", count, " Seiten in",exec_time / 60, "Minuten verarbeitet!"
print "Das sind pro Sekunde", count / exec_time, "Seiten :)"

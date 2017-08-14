EXPANDDBLIST=../multiversion/bin/expanddblist

all: flow.dblist nowikidatadescriptiontaglines.dblist wikipedia-english.dblist

clean:
	rm -f flow.dblist nowikidatadescriptiontaglines.dblist wikipedia-english.dblist

flow.dblist:
	${EXPANDDBLIST} flow-computed.dblist > flow.dblist

nowikidatadescriptiontaglines.dblist:
	${EXPANDDBLIST} nowikidatadescriptiontaglines-computed.dblist > nowikidatadescriptiontaglines.dblist

wikipedia-english.dblist:
	${EXPANDDBLIST} wikipedia-english-computed.dblist > wikipedia-english.dblist

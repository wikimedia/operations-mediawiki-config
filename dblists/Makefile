EXPANDDBLIST=../multiversion/bin/expanddblist

all: flow-labs.dblist nowikidatadescriptiontaglines.dblist wikipedia-english.dblist

clean:
	rm -f flow-labs.dblist nowikidatadescriptiontaglines.dblist wikipedia-english.dblist

flow-labs.dblist:
	cat flow.dblist all-labs.dblist flow_only_labs.dblist | sort | uniq -d > flow-labs.dblist

nowikidatadescriptiontaglines.dblist:
	${EXPANDDBLIST} nowikidatadescriptiontaglines-computed.dblist > nowikidatadescriptiontaglines.dblist

wikipedia-english.dblist:
	${EXPANDDBLIST} wikipedia-english-computed.dblist > wikipedia-english.dblist

from collections import defaultdict
import csv
import json
import sys
import os
import base64



filename = base64.b64decode(sys.argv[1])
print("%s is the file to convert" % filename)
# create dictionary to hold the data in 'json' format
data = defaultdict(list)

# open the .csv and parse the data
with open("%s" % filename) as csv_file:
    csv_reader = csv.reader(csv_file, delimiter=',')

    count = 0;
    for row in csv_reader:
        if count == 0:
            count += 1
            continue

        print('%s is source %s is target' % (row[1], row[2]))

        data['links'].append({"source": row[1], "target": row[2]})

        data['nodes'].append({
            "id": row[1],
            "name": str(row[1]),
            "match": 1.0,
            "artist": "Dummy",
            "playcount": 1000000,
            "origin": str(row[3]) # make it colored by this and NOT artist
        })

json = json.dumps(data)

os.chmod(filename, 0o777)

f = open("%s.json" % filename, "w+")
f.write(json)
f.close()
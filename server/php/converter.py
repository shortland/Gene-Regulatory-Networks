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

    #count = 0;
    for row in csv_reader:
    #    if count == 0:
    #        count += 1
    #        continue

        print('%s is source %s is target' % (row[0], row[1]))

        data['edges'].append({"from": row[0], "to": row[1]})

        data['nodes'].append({
            "id": row[0],
            "title": "Node Id: %s<br>Destination Id: %s" % (str(row[0]), str(row[1])),
            "group": row[2],
        })

json = json.dumps(data)

os.chmod(filename, 0o777)

f = open("%s.json" % filename, "w+")
f.write(json)
f.close()
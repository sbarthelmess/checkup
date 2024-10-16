#!/bin/bash
# =-=-=-=-=-=-=
# Configure variables to your environment
IPLIST="/opt/checkup/cron/antennas.ini"
DATAFILE="/opt/checkup/www/checkup_data.csv.gz"

# Main app from here on
# =-=-=-=-=-=-=-=-=-=-=
pings=`/usr/bin/fping -D -c3 -q < ${IPLIST} 2>/dev/stdout`
while IFS= read -r line; do
  echo $line | sed 's/ //g;s/xmt\/rcv\/\%loss\=//g;s/min\/avg\/max\=//g' | awk -F'[:/]' '{print d,$1,$2,$3,$4,$5,$6}' d="$(date +%m-%d-%Y,%H%M%S)" OFS="," | gzip -9 >> ${DATAFILE}
done <<< "$pings"

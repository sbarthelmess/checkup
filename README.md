# CheckUp Application
Ping a bunch of ips/servers (in cron) and view them nicely via a web interface.  I originally needed this to monitor a bunch of antennas and not knowing which ones were at fault.

- Utilize STOCK fping for FAST, efficient pinging
- Save to simple, reliable, readable CSV format
- Compress CSV output (gzip)
- Diagram my network (in mermaid mockup, as an example)
  
My example network to monitor (with a fairly complex topology):
```mermaid
flowchart TB
  M1["Xfinity<br>Modem"]
  R1["Main Router & FW<br>10.0.0.1"]
  A1((("Antenna #1<br>192.168.100.13")))
  A2((("Antenna #2<br>192.168.100.12")))
  A3((("Antenna #3<br>192.168.100.11")))
  A4((("Antenna #4<br>192.168.100.10")))
  FW1["1st Network FW<br>192.168.100.50/24"]
  FW2["2nd Network FW<br>192.168.0.1/24"]
  FW3["Home Network FW<br>192.168.5.1/24"]

  subgraph Internet
    M1 --> R1
  end
  R1 --> A1
  subgraph Antennas
    A1 --> A2
    A2 --> A3
    A3 --> A4
  end
  subgraph 1st Neighbor Subnet
    R1 --> FW1
  end
  subgraph 2nd Neighbor Subnet
    A2 --> FW2
  end
  subgraph Final Home Network
    A4 --> FW3
  end
```

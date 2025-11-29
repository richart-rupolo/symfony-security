// init-rs.js
try {
  rs.initiate({
    _id: "rs0",
    members: [
      { _id: 0, host: "symfony-mongodb:27017" }
    ]
  });
} catch (e) {
  print("ReplicaSet init skipped:", e);
}

var attempt = 0;
while (attempt < 30) {
  var status = rs.status().myState;
  if (status === 1) {
    print("ReplicaSet READY (PRIMARY)");
    break;
  }
  print("Waiting for PRIMARY state...");
  sleep(1000);
  attempt++;
}

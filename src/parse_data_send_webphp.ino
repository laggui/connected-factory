#include <Ethernet.h>
#include <SPI.h>

#define SOP '<' // Start of packet
#define EOP '>' // End of packet
#define PAD "-" // Packet (device) address delimiter
// Example of a packet: <1-1022> ---> device address = 1, data = 1022

// Ethernet config
byte mac[] = { 0x90, 0xA2, 0xDA, 0x0F, 0x2A, 0x8D };
byte ip[] = { 192, 168, 2, 77 };
byte gw[] = {192, 168, 2, 1};
byte subnet[] = { 255, 255, 255, 0 };

EthernetClient client;//(server, 80);

byte server[] = { 192, 168, 2, 83  }; // Server IP (static)

// Data config -- information to be sent to the web server
int device = 0;
int data = 0;

const byte buff_size = 16;
char in_buffer[buff_size];
char tmp_buffer[buff_size];

boolean newData = false;

void setup() {
  Serial.begin(9600);
  Ethernet.begin(mac, ip, gw, gw, subnet);
  Serial.println(F("Program running..."));
  delay(1000);
}

void loop() {
  extractSerial();
  showNewData();
  //sttmppy(tmp_buffer, in_buffer);
}

void extractSerial() {
  static boolean recvInProgress = false;
  static byte index = 0;
  char tmp;

  while (Serial.available() > 0 && newData == false) {
    tmp = Serial.read();

    if (recvInProgress == true) {
      if (tmp != EOP) {
        in_buffer[index] = tmp;
        index++;
        if (index >= buff_size) {
          index = buff_size - 1;
        }
      }
      else {
        in_buffer[index] = '\0'; // terminate the string
        recvInProgress = false;
        index = 0;
        newData = true;
      }
    }

    else if (tmp == SOP) {
      recvInProgress = true;
    }
  }
}

void showNewData() {
  if (newData == true) {
    //Serial.print(F("Buffer:  "));
    //Serial.println(in_buffer);
    strcpy(tmp_buffer, in_buffer);
    parseData();
    //showParsedData();
    sendData();
    newData = false;
  }
}

void parseData() {
  char * strtokIndex; // this is used by strtok() as an iindex

  strtokIndex = strtok(tmp_buffer, PAD);     // get the first part - the string
  device = atoi(strtokIndex);

  strtokIndex = strtok(NULL, PAD); // this continues where the previous call left off
  data = atoi(strtokIndex);     // convert this part to an integer
}


void showParsedData() {
  Serial.print(F("Device: "));
  Serial.println(device);
  Serial.print(F("Data: "));
  Serial.println(data);
}

void sendData()
{

  Serial.println();
  Serial.println(F("ATE :)"));
  //delay(1000);                                    //Keeps the connection from freezing

  if (client.connect(server, 80)) {
    Serial.println("Connected");
    client.print("GET /insertdata.php?");
    client.print("device=");
    client.print(device);
    client.print("&data=");
    client.print(data);
    client.println(" HTTP/1.1");
    client.println("Host: 192.168.2.83");
    client.println("Connection: close");
    client.println();
    Serial.println();
    while (client.connected()) {
      while (client.available()) {
        Serial.write(client.read());
      }
    }
  }
  else
  {
    Serial.println(F("Connection unsuccessful"));
  }
  //stop client
  client.stop();
  while (client.status() != 0)
  {
    delay(5);
  }
}


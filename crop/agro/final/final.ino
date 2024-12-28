#include <WiFi.h>
#include <HTTPClient.h>
#include <Wire.h>

// WiFi credentials
const char* ssid = "GKWIFI";
const char* password = "12345678";

// PHP script URL
const char* serverName = "http://192.168.226.56/insertagro.php"; // Replace with your XAMPP server's IP

// RE and DE Pins set the RS485 module
#define RE 5
#define DE 18

// Modbus RTU requests
const byte nitro[] = {0x01, 0x03, 0x00, 0x1e, 0x00, 0x01, 0xe4, 0x0c};
const byte phos[] = {0x01, 0x03, 0x00, 0x1f, 0x00, 0x01, 0xb5, 0xcc};
const byte pota[] = {0x01, 0x03, 0x00, 0x20, 0x00, 0x01, 0x85, 0xc0};
const byte moisture[] = {0x01, 0x03, 0x00, 0x21, 0x00, 0x01, 0xd5, 0xc0};

// Analog pin for pH sensor
const int potPin = A0;

byte values[11];
float ph;

void setup() {
  Serial.begin(115200);

  // Initialize WiFi
  WiFi.begin(ssid, password);
  Serial.print("Connecting to WiFi");
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println("\nConnected to WiFi!");

  // RS485 pin modes
  pinMode(RE, OUTPUT);
  pinMode(DE, OUTPUT);

  // Analog pin mode
  pinMode(potPin, INPUT);

  Serial2.begin(9600, SERIAL_8N1, 19, 21); // RX, TX pins
}

void loop() {
  byte nitrogenValue = nitrogen();
  byte phosphorusValue = phosphorous();
  byte potassiumValue = potassium();
  byte soilMoistureValue = soilMoisture();

  int analogValue = analogRead(potPin);
  float voltage = analogValue * (3.3 / 4095.0);
  ph = (3.3 * voltage);

  Serial.println("Sending data to server...");
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;

    // Form the POST request
    http.begin(serverName);
    http.addHeader("Content-Type", "application/x-www-form-urlencoded");

    String postData = "nitrogen=" + String(nitrogenValue) +
                      "&phosphorus=" + String(phosphorusValue) +
                      "&potassium=" + String(potassiumValue) +
                      "&soil_moisture=" + String(soilMoistureValue) +
                      "&ph=" + String(ph);

    int httpResponseCode = http.POST(postData);

    if (httpResponseCode > 0) {
      Serial.print("HTTP Response code: ");
      Serial.println(httpResponseCode);
      String response = http.getString();
      Serial.println("Response: " + response);
    } else {
      Serial.print("Error code: ");
      Serial.println(httpResponseCode);
    }
    http.end();
  } else {
    Serial.println("WiFi Disconnected");
  }

  delay(2000); // Delay between measurements
}

byte nitrogen() {
  return readModbusValue(nitro);
}

byte phosphorous() {
  return readModbusValue(phos);
}

byte potassium() {
  return readModbusValue(pota);
}

byte soilMoisture() {
  return readModbusValue(moisture);
}

byte readModbusValue(const byte* request) {
  digitalWrite(DE, HIGH);
  digitalWrite(RE, HIGH);
  delay(10);
  if (Serial2.write(request, 8) == 8) {
    digitalWrite(DE, LOW);
    digitalWrite(RE, LOW);
    for (byte i = 0; i < 7; i++) {
      values[i] = Serial2.read();
    }
  }
  return values[4];
}

#include <Wire.h>

// RE and DE Pins set the RS485 module
// to Receiver or Transmitter mode
#define RE 5  // Replace with ESP32 pin D5
#define DE 18 // Replace with ESP32 pin D18

// Modbus RTU requests for reading NPK and moisture values
const byte nitro[] = {0x01, 0x03, 0x00, 0x1e, 0x00, 0x01, 0xe4, 0x0c}; // Nitrogen
const byte phos[] = {0x01, 0x03, 0x00, 0x1f, 0x00, 0x01, 0xb5, 0xcc}; // Phosphorous
const byte pota[] = {0x01, 0x03, 0x00, 0x20, 0x00, 0x01, 0x85, 0xc0}; // Potassium
const byte moisture[] = {0x01, 0x03, 0x00, 0x21, 0x00, 0x01, 0xd5, 0xc0}; // Soil Moisture

// A variable used to store NPK and soil moisture values
byte values[11];

// Using Serial2 for ESP32 (RX = D19, TX = D21)
#define RXD2 19 // Replace with ESP32 pin D19
#define TXD2 21 // Replace with ESP32 pin D21

// Analog pin for pH sensor
const int potPin = A0;
float ph;
float analogValue = 0;

void setup() {
  // Set the baud rate for the Serial port
  Serial.begin(115200);

  // Initialize Serial2 with specified RX and TX pins
  Serial2.begin(9600, SERIAL_8N1, RXD2, TXD2);

  // Define pin modes for RE and DE
  pinMode(RE, OUTPUT);
  pinMode(DE, OUTPUT);

  // Configure the analog pin for pH measurement
  pinMode(potPin, INPUT);

  delay(1000);
}

void loop() {
  // Read NPK values
  byte nitrogenValue, phosphorusValue, potassiumValue, soilMoistureValue;
  nitrogenValue = nitrogen();
  delay(250);
  phosphorusValue = phosphorous();
  delay(250);
  potassiumValue = potassium();
  delay(250);

  // Read soil moisture
  soilMoistureValue = soilMoisture();
  delay(250);

  // Read pH value
  analogValue = analogRead(potPin);
  float voltage = analogValue * (3.3 / 4095.0); // Convert analog value to voltage
  ph = (3.3 * voltage); // Convert voltage to pH value (requires calibration)

  // Print NPK values
  Serial.print("Nitrogen: ");
  Serial.print(nitrogenValue);
  Serial.println(" mg/kg");
  Serial.print("Phosphorous: ");
  Serial.print(phosphorusValue);
  Serial.println(" mg/kg");
  Serial.print("Potassium: ");
  Serial.print(potassiumValue);
  Serial.println(" mg/kg");

  // Print soil moisture
  Serial.print("Soil Moisture: ");
  Serial.print(soilMoistureValue);
  Serial.println(" %");

  // Print pH value
  Serial.print("pH: ");
  Serial.println(ph);

  delay(2000); // Delay before next cycle
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
  return readModbusValue(moisture); // Retrieve soil moisture data
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
  return values[4]; // Return the measured value from the response
}

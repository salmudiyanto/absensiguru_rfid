#include <Arduino.h>

#include <ESP8266WiFi.h>
#include <ESP8266WiFiMulti.h>

#include <ESP8266HTTPClient.h>
#include <ESP8266WebServer.h>

#include <WiFiClient.h>
#include <ArduinoJson.h>
ESP8266WiFiMulti WiFiMulti;

#include <Wire.h> 
#include <LiquidCrystal_I2C.h>

LiquidCrystal_I2C lcd(0x27,16,2);  // set the LCD address to 0x27 for a 16 chars and 2 line display



#include <SPI.h>
#include <RFID.h>
#define SDA_PIN 2
#define RST_PIN 0
#define RELAY 15
RFID rfid(SDA_PIN,RST_PIN);
const char* ssid = "halisaa";
const char* password = "14101410";
ESP8266WebServer server(80);  //--> Server on port 80

int serNum[5];          //Variable buffer Scan Card

void setup()
{
  Serial.begin(115200);
  Serial.println();
  Serial.println();
  Serial.println();
  pinMode(RELAY,OUTPUT); 
  digitalWrite(RELAY, HIGH);
  lcd.init();                      // initialize the lcd 
  lcd.init();
  // Print a message to the LCD.
  lcd.backlight();
  lcd.setCursor(0,0);
  lcd.print("Connecting!");

  for (uint8_t t = 4; t > 0; t--) {
    Serial.printf("[SETUP] WAIT %d...\n", t);
    Serial.flush();
    delay(1000);
  }
  WiFi.mode(WIFI_STA);
  WiFiMulti.addAP(ssid, password);                      

  while (WiFiMulti.run() != WL_CONNECTED) {
    Serial.print(".");
    delay(1000);
  }

  Serial.println("");
  Serial.print("Successfully connected to : ");
  Serial.println(ssid);
  Serial.print("IP address: ");
  Serial.println(WiFi.localIP());

  lcd.clear();
  lcd.setCursor(0,0);
  lcd.print("Connected");


  
  SPI.begin();
  rfid.init();

  

  lcd.clear();
  lcd.setCursor(0,0);
  lcd.print("Ready");
  delay(1000);

}

void loop()
{
  lcd.setCursor(0,0);
  lcd.print("Silahkan Scan!");
  lcd.setCursor(0,1);
  lcd.print("Absen Doorlock");

  if(rfid.isCard())
  {
    if(rfid.readCardSerial())
    {
      String UIDresultSend ="", postData;
      // UIDresultSend = StrUID;
        doorLock();

      Serial.print("Kode Tag E-KTP");
      Serial.print(" : ");
      Serial.print(rfid.serNum[0]);
      Serial.print(" ");
      UIDresultSend += rfid.serNum[0];
      Serial.print(rfid.serNum[1]);
      Serial.print(" ");
      UIDresultSend += rfid.serNum[1];
      Serial.print(rfid.serNum[2]);
      Serial.print(" ");
      UIDresultSend += rfid.serNum[2];
      Serial.print(rfid.serNum[3]);
      Serial.print(" ");
      UIDresultSend += rfid.serNum[3];
      Serial.print(rfid.serNum[4]);
      Serial.println("");
      UIDresultSend += rfid.serNum[4];

      WiFiClient client;
      HTTPClient http;    //Declare object of class HTTPClient

    
      //Post Data
      postData = "id=" + UIDresultSend;

      http.begin(client, "http://isnurlisa.balconteach.my.id/rfid");  //Specify request destination
      http.addHeader("Content-Type", "application/x-www-form-urlencoded"); //Specify content-type header
    
      int httpCode = http.POST(postData);   //Send the request
      String payload = http.getString();
      StaticJsonDocument<200> jsonRespon;
      DeserializationError error = deserializeJson(jsonRespon, payload);  
      lcd.clear();
      lcd.setCursor(0,0);
      lcd.print("ID:");
      lcd.print(UIDresultSend);
      // lcd.setCursor(0,1);
      // lcd.print("Payload:");
      Serial.print(payload);
      if(error){
        lcd.clear();
        Serial.println("Kesalahan");
        Serial.println(error.c_str());
        delay(5000);
        return;
      }

      int statusPintu = jsonRespon["status"];
      String namaGuru = jsonRespon["nama"];
      int statusAbsen = jsonRespon["absen"];
      int statusScan = jsonRespon["scan"];

      if(statusPintu == 0){
        if(statusAbsen == 0){
          lcd.clear();
          lcd.setCursor(0,0);
          lcd.print("RFID di Tolak");
          lcd.setCursor(0,1);
          lcd.print(namaGuru);
          delay(5000);  
        }else{
          if(statusScan==0){
            lcd.clear();
            lcd.setCursor(0,0);
            lcd.print("Gagal Melakukan");
          }else{
            lcd.clear();
            lcd.setCursor(0,0);
            lcd.print("Sukses Melakukan");
          }
          lcd.setCursor(0,1);
          lcd.print("Scan KTP Baru");
          delay(5000); 
        }
        
      }else{
        Serial.println(namaGuru);
        lcd.clear();
        lcd.setCursor(0,0);
        lcd.print("Pintu Terbuka");
        lcd.setCursor(0,1);
        lcd.print(namaGuru);
        doorLock();
        delay(5000);
      }
      
      
//      if(payload.toInt() > 0){
//        doorLock();
//        lcd.clear();
//        Serial.print("pintu telah terbuka");
//        delay(5000);
//      }else if(payload.toInt() == 0){
//        lcd.clear();
//        lcd.setCursor(0,0);
//        lcd.print(payload);
//        lcd.setCursor(0,1);
//        delay(5000);
//      }else{
//        lcd.clear();
//        lcd.setCursor(0,0);
//        lcd.setCursor(0,1);
//        delay(5000);
//      }
      http.end();  //Close connection6.6
      delay(1000);
    }
      lcd.clear();
  }
  rfid.halt();
  delay(1000);

}

void doorLock(){
    delay(1000);
//    lcd.setCursor(0,0); 
//    lcd.print("pintu terbuka");
    digitalWrite(RELAY, LOW);//buka
    delay(5000);
    digitalWrite(RELAY, HIGH);//tutup
}

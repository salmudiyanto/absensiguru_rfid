#include <OneWire.h>
#include <DallasTemperature.h>
#include <Servo.h>
#include <LiquidCrystal_I2C.h>


LiquidCrystal_I2C lcd(0x27, 16, 2);

Servo myservo;  // create servo object to control a servo
#define buka 130 
#define tutup 90 

//tank
#define Tank_Pin 6
#define echo_Tank 7

//tank
float tanknya,kolamnya,embernya;
unsigned long lastTank = 0;
unsigned long lastKolam = 0;
unsigned long lastEmber = 0;

//pakan
unsigned long lastPakan = 0;
unsigned long tundaPakan = 40000;

unsigned long tunda = 1000;
unsigned long lastLCD = 0;
unsigned long tundaLCD = 3000;
int samples = 10;
float adc_resolution = 1024.0;

int lastmo=0,mo=0,isi=0,buang=0,berat=0,jumlah=0,pakan=0,stat=0;
String msg = "", lastMsg= "";


void setup() {
  // put your setup code here, to run once:
  Serial.begin(9600);
  Serial3.begin(115200);
  
  pinMode(Tank_Pin, OUTPUT);
  pinMode(echo_Tank, INPUT);

  myservo.attach(11);
  myservo.write(tutup);
  lcd.init();
  lcd.backlight();
  lcd.setCursor(0,0);
  lcd.print("MONITORING LELE");
  lcd.setCursor(0,1);
  lcd.print("Set Up");
  lastLCD = millis();
  delay(3000);

}

void makan()
{
  if ((millis() - lastPakan) > tundaPakan) {
    Serial.println("Kasih Makan");
    myservo.write(buka);
    delay(berat*3);
    myservo.write(tutup);
    lastPakan=millis();
  }
}

void cektank()
{
  int pulse, cm;
  float bawah = 23, atas=5;
  if ((millis() - lastTank) > tunda) {
    digitalWrite(Tank_Pin,LOW);
    delayMicroseconds(2);
    digitalWrite(Tank_Pin, HIGH);
    delayMicroseconds(10);
    digitalWrite(Tank_Pin, LOW);
    pulse = pulseIn(echo_Tank, HIGH);
    cm = pulse * 0.034 / 2;
  
    Serial.print("Jarak (cm) : ");
    Serial.println(cm);
    tanknya = ((100/(atas-bawah))*cm)+(((bawah*100)*-1)/(atas-bawah));
    Serial.print("Persen : ");
    Serial.println(tanknya);
    lastTank=millis();
  }
}


String splitString(String data, char separator, int index)
{
    int found = 0;
    int strIndex[] = { 0, -1 };
    int maxIndex = data.length() - 1;

    for (int i = 0; i <= maxIndex && found <= index; i++) {
        if (data.charAt(i) == separator || i == maxIndex) {
            found++;
            strIndex[0] = strIndex[1] + 1;
            strIndex[1] = (i == maxIndex) ? i+1 : i;
        }
    }
    return found > index ? data.substring(strIndex[0], strIndex[1]) : "";
}

void loop() {
  // put your main code here, to run repeatedly:
  cektank();
  if ((millis() - lastLCD) > tundaLCD) {
    if(stat==0)stat=1;
    else stat=0;
    lastLCD = millis();
  }
  Serial3.println("0;0;0;0;"+(String)tanknya);
  if(Serial3.available()){
    msg = Serial3.readStringUntil('\n');
    if(lastMsg==msg){
      mo = splitString(msg, ';', 0).toInt();
      buang = splitString(msg, ';', 1).toInt();
      isi = splitString(msg, ';', 2).toInt(); 
      berat = splitString(msg, ';', 3).toInt();
      jumlah = splitString(msg, ';', 4).toInt();
      pakan = splitString(msg, ';', 5).toInt();  
    }
    lastMsg=msg;
    Serial.println(msg);
  }
  if(pakan==1){
    makan();
  }
  delay(100);
}

#include <FirebaseArduino.h>
#include <ESP8266HTTPClient.h>
#include <ESP8266WiFi.h>
#include <dht.h>
#include <stdio.h>

dht DHT;

#define FIREBASE_AUTH "Cuz3c10AB6PICdhMHgFdWmZUNQSx5VrehWJBB85g"
#define FIREBASE_HOST "arduino-projects-74ff9.firebaseio.com"

#define WIFI_SSID "ydev"
#define WIFI_PASS "yash1234"
#define thPin D2
#define mPin A0
#define irriPin D7
#define shadePin D6
#define fanPin D5
#define exhaustPin D4
#define WIFI_STATUS_PIN D8

char temperature[10]; 
char humidity[10];
char moisture[10];
char reqBody[100];

void setup() {
  // put your setup code here, to run once:
  Serial.begin(9600);
  
  
  pinMode(irriPin, OUTPUT);
  pinMode(shadePin, OUTPUT);
  pinMode(fanPin, OUTPUT);
  pinMode(exhaustPin, OUTPUT);
  
  pinMode(WIFI_STATUS_PIN, OUTPUT);
    
  WiFi.begin(WIFI_SSID, WIFI_PASS);
  Serial.print("connecting");
  while (WiFi.status() != WL_CONNECTED) {
    Serial.print(".");
    delay(500);
  }
  Serial.println();
  Serial.print("connected: ");
  Serial.println(WiFi.localIP());

  digitalWrite(WIFI_STATUS_PIN,HIGH);

  firebaseInitialization();
  sendDataToFirebase(0, 0, 0);


}

void firebaseInitialization(){
  Firebase.begin(FIREBASE_HOST, FIREBASE_AUTH);
}

void firebasereconnect(){
    firebaseInitialization();
}

void loop() {
  // put your main code here, to run repeatedly:

  while (WiFi.status() != WL_CONNECTED) {
    Serial.print(".");
    digitalWrite(WIFI_STATUS_PIN, LOW);
    delay(500);
  }
    
  digitalWrite(WIFI_STATUS_PIN, HIGH);
  

  if (Firebase.failed()) {
      Serial.print(".");
      //Serial.println(Firebase.error());  
      firebasereconnect();
      return;
  }
  
  
  getTMH();
  delay(100);
}

void getTMH(){
   int count = 0;
  float temp = 0;
   float hum = 0 ;
   int moi = 0;
  while(count<6){  
  int readData = DHT.read11(thPin);
  temp = temp + (DHT.temperature);
  moi = moi + map(analogRead(mPin),0,1024,0,100);
  hum = hum +  (DHT.humidity);
  count++;
  Serial.println("*****************************************************");
  Serial.print("fTemperatur  = ");
  Serial.print(temp);
  Serial.print("  fMoisture = ");
  Serial.print(moi);
  Serial.print("  fHumidity = ");
  Serial.println(hum);

  checkIfToIrrigateFarm();
  checkIfShadeIsOn();
  checkIfFansInOn();
  checkIfExhaustIsOn();
  
  delay(5000);
  
  }

  hum = hum/6;
  temp = temp/6;
  moi = moi/6;

  sendDataToFirebase(temp, moi, hum);
  Serial.println("*****************************************************");
  Serial.print("fTemperatur  = ");
  Serial.print(temp);
  Serial.print("  fMoisture = ");
  Serial.print(moi);
  Serial.print("  fHumidity = ");
  Serial.println(hum);
  Serial.println("*****************************************************");
  
  Serial.println("*****************************************************");

  while (WiFi.status() != WL_CONNECTED) {
    Serial.print(".");
    digitalWrite(WIFI_STATUS_PIN, LOW);
    delay(500);
  }
  postDataToServer(temp, moi, hum);
  delay(100);
  
}

void checkIfFansInOn(){
  char ir[5];
  Firebase.getString("fan").toCharArray(ir,sizeof(ir));
  if(strcmp(ir, "true") == 0){
      Serial.println("Fan-->true");
      digitalWrite(fanPin, HIGH);
      delay(100);
  }else{
    Serial.println("Fan-->False");
    digitalWrite(fanPin, LOW);
    delay(100);
  }
}

void checkIfToIrrigateFarm(){

  char ir[5];
  Firebase.getString("irrigate").toCharArray(ir,sizeof(ir));
  if(strcmp(ir, "true") == 0){
      Serial.println("Irrigate-->true");
      digitalWrite(irriPin, HIGH);
      delay(100);
  }else{
    Serial.println("Irrigate-->False");
    digitalWrite(irriPin, LOW);
    delay(100);
  }
}

void checkIfShadeIsOn(){
  char ir[5];
  Firebase.getString("shade").toCharArray(ir,sizeof(ir));
  if(strcmp(ir, "true") == 0){
      Serial.println("Shade-->true");
      digitalWrite(shadePin, HIGH);
      delay(100);
  }else{
    Serial.println("Shade-->False");
    digitalWrite(shadePin, LOW);
    delay(100);
  }
}


void checkIfExhaustIsOn(){
  char ir[5];
  Firebase.getString("exhaust").toCharArray(ir,sizeof(ir));
  if(strcmp(ir, "true") == 0){
      Serial.println("Exhaust-->true");
      digitalWrite(exhaustPin, HIGH);
      delay(100);
  }else{
    Serial.println("Exhaust-->False");
    digitalWrite(exhaustPin, LOW);
    delay(100);
  }
}

void sendDataToFirebase(float t, int m, float h){

   gcvt(t, 10, temperature);
   gcvt(h, 10, humidity);
   itoa(m, moisture, 10);
  
  Firebase.set("temperature",temperature);
  Firebase.set("moisture",moisture);
  Firebase.set("humidity",humidity);
  
  Serial.print("Temperatur  = ");
  Serial.print(temperature);
  Serial.print("  Moisture = ");
  Serial.print(moisture);
  Serial.print("  Humidity = ");
  Serial.print(humidity);
  Serial.println();
}

void postDataToServer(float t, int m, float h){

    HTTPClient http;
   
   http.begin("http://yrtwebhosting-com.stackstaging.com/arduino/index.php");      //Specify request destination
   http.addHeader("Content-Type", "application/x-www-form-urlencoded");  //Specify content-type header
   sprintf(reqBody, "temperature=%f&humidity=%f&moisture=%d", t, h, m);
   Serial.print("Resquest URL");
   Serial.print(reqBody); 
   Serial.println();
   //String reqBody = "temperature="+t+"&humidity="+h;
   int httpCode = http.POST(reqBody);   //Send the request
   String payload = http.getString();                  //Get the response payload

   Serial.println(httpCode);   //Print HTTP return code
   Serial.print("Response: ");
   Serial.print(payload);    //Print request response payload
   Serial.println();
   http.end();  //Close connection

  
}






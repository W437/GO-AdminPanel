
Mandatory Setup
Table of Contents

    Map Configuration [SS]
    Business Setup
    Mail Configuration 
    Firebase Configuration (for notification) 
    Payment Configuration 
    SMS Module Configuration 

Map Configuration [SS]

Client should buy Map API from Google for enabling the maps into the panels. Without buying those APIs clients cannot load Google maps into the panels for selecting zones. For generating map api key you can watch this video. Now go to your admin panel then “Third party APIs” menu, here you will find two inputs for map api key client and map api key server. You can restrict the client with admin panel domain and the server key with your server ip address. If you don’t want any restriction then you can use single api key for both field.

TIP

Recommended tutorial is below 👇

Business Setup

In the admin panel we have a menu called Business Setup where you can set your logo, timezone, country, time format, location, currency and many more things.
Mail Configuration 

Mail Configurations part admin can set his Mailer name, host, driver, user name, Email Id and his own encryption method and password for this SMTP Mail setup. This configuratin is used for sending password recovery mail for restaurant and other mail templates.
Firebase Configuration (for notification) 

The Firebase Push Notification will send messages for general notifications, chatting notification, order place notification and every order status notification. To set up firebase notification go to admin panel 3rd Party & Configuration > Firebase Notification > Firebase Configuration.

    Go to https://console.firebase.google.com/
    If you don’t have a project, create one.
    Click on the settings icon from left sidebar (beside Project Overview) & Go to Project Settings.
    From the Project Settings, go to Service Accounts tab.
    Click on Generate new private Key to generate the key. It will automatically download a .json file.
    Open the file with any text editor, copy the contents in it, and add those to Service File Content in 3rd Party > Push Notification > Firebase Configuration in admin panel.

Tip

Recommended tutorial is below 👇

The Firebase Push Notification will send messages for every order status. If the admin turns on the status then with order status change customers, restaurant, delivery man will get status notification and if he turned off that then no one will get that message. To set up firebase notification go to the admin panel Notification Settings menu.

Before that download the JavaScript file firebase-messaging-sw.js from this following link: https://drive.google.com/file/d/1C4TpwYD6P5kkd8FA7xC333lXv10pO3hz/view?usp=sharing

In the JavaScript file “firebase-messaging-sw.js” replace your firebase credential ( apiKey, authDomain, projectId, storageBucket, messagingSenderId, appId ):

firebase.initializeApp({

    apiKey: "YOUR_API_KEY",

    authDomain: "YOUR_AUTH_DOMAIN",

    projectId: "YOUR_PROJECT_ID",

    storageBucket: "YOUR_STORAGE_BUCKET",

    messagingSenderId: "YOUR_MESSAGING_SENDER_ID",

    appId: "YOUR_APP_ID",

    databaseURL: "...",

});

Payment Configuration 

In this part Admin will introduced with the payment gateways. Cash on delivery, Digital Payment like SSLCOMMERZ, Razor pay, Paypal, Stripe, Paystack, Senang Pay, Flutterwave, MercadoPago, Payment accept are available for payment gateways. He can make the necessary setup of making the status active and inactive of those payment gateways as well.
SMS Module Configuration 

SMS Module is used for SMS Gateways for OTP sending in the simplest way of user verification. Customer will get OTP when they create their own account and for password recovery.
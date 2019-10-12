const firebaseConfig = {
    apiKey           : "AIzaSyBglHISyB36SibOQ2MWH_3SEN-MKwc4_1k",
    authDomain       : "massage-blind.firebaseapp.com",
    databaseURL      : "https://massage-blind.firebaseio.com",
    storageBucket    : "massage-blind.appspot.com",
    projectId        : "massage-blind",
    messagingSenderId: "938207189411",
    appId            : "1:938207189411:web:fa637e77cd8da172"
  };


firebase.initializeApp({
    apiKey    : firebaseConfig.apiKey,
    authDomain: firebaseConfig.authDomain,
    projectId : firebaseConfig.projectId
  });
  
var db = firebase.firestore();


db.collection("cities").doc("SF")
    .onSnapshot(function(doc) {
        console.log("Current data: ", doc.data());
});

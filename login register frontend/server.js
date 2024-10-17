require('dotenv').config();  // To load environment variables from .env file
const express = require('express');
const bodyParser = require('body-parser');
const path = require('path');
const twilio = require('twilio');
const db = require('./db');

// Load Twilio configuration from environment variables
const accountSid = process.env.TWILIO_ACCOUNT_SID;
const authToken = process.env.TWILIO_AUTH_TOKEN;
const serviceSid = process.env.TWILIO_SERVICE_SID; // Service SID for Twilio Verify
const client = twilio(accountSid, authToken);

const app = express();
app.use(bodyParser.json());
app.use(express.static('public')); // Serve static files from "public" folder

// Endpoint to send OTP
app.post('/send-otp', (req, res) => {
  const { aadhar, phone } = req.body;

  // Check if the Aadhar number and phone match the database
  db.get("SELECT * FROM users WHERE aadhar_number = ? AND phone_number = ?", [aadhar, phone], (err, user) => {
    if (err) {
      return res.status(500).json({ success: false, message: 'Database error' });
    }

    if (!user) {
      return res.status(400).json({ success: false, message: 'Aadhar or phone number not found' });
    }

    // Use Twilio Verify API to send OTP
    client.verify.v2.services(serviceSid)
      .verifications
      .create({ to: phone, channel: 'sms' })
      .then(verification => res.json({ success: true, message: 'OTP sent', sid: verification.sid }))
      .catch(error => res.status(500).json({ success: false, message: 'Failed to send OTP', error: error.message }));
  });
});

// Endpoint to verify OTP
app.post('/verify-otp', (req, res) => {
  const { phone, otp } = req.body;

  // Use Twilio Verify API to verify OTP
  client.verify.v2.services(serviceSid)
    .verificationChecks
    .create({ to: phone, code: otp })
    .then(verification_check => {
      if (verification_check.status === 'approved') {
        res.json({ success: true, message: 'OTP verified successfully' });
      } else {
        res.status(400).json({ success: false, message: 'Invalid OTP' });
      }
    })
    .catch(error => res.status(500).json({ success: false, message: 'Failed to verify OTP', error: error.message }));
});

// Start the server
const PORT = process.env.PORT || 3000;
app.listen(PORT, () => {
  console.log(`Server running on port ${PORT}`);
});

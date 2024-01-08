// app.js
const express = require('express');
const path = require('path');
const open = require('open');
const axios = require('axios');
const bodyParser = require('body-parser');

const app = express();
const PORT = 3000;

// Your Spotify API credentials
const SPOTIFY_CLIENT_ID = 'your_client_id';
const SPOTIFY_CLIENT_SECRET = 'your_client_secret';
const REDIRECT_URI = `http://localhost:3000/callback`;

let accessToken = '';

// Serve static files
app.use(express.static(path.join(__dirname, 'public')));
app.use(bodyParser.urlencoded({ extended: true }));

// Redirect to Spotify for authentication
app.get('/login', (req, res) => {
  const scopes = 'playlist-read-private'; // Add more scopes as needed
  res.redirect(`https://accounts.spotify.com/authorize?client_id=${SPOTIFY_CLIENT_ID}&redirect_uri=${REDIRECT_URI}&scope=${encodeURIComponent(scopes)}&response_type=code`);
});

// Callback after Spotify has authenticated the user
app.get('/callback', async (req, res) => {
  const code = req.query.code;

  try {
    const response = await axios.post('https://accounts.spotify.com/api/token', {
      grant_type: 'authorization_code',
      code,
      redirect_uri: REDIRECT_URI,
      client_id: SPOTIFY_CLIENT_ID,
      client_secret: SPOTIFY_CLIENT_SECRET,
    });

    accessToken = response.data.access_token;

    // Redirect to the dashboard or perform other actions as needed
    res.redirect('/dashboard');
  } catch (error) {
    console.error('Error exchanging code for access token:', error.message);
    res.status(500).send('Internal Server Error');
  }
});

// Dashboard route
app.get('/dashboard', (req, res) => {
  // Render your dashboard with the access token available
  // (Handle accessToken expiration and refresh in a real app)
  res.sendFile(path.join(__dirname, 'public', 'dashboard.html'));
});

// Start the server
app.listen(PORT, () => {
  console.log(`Server is running on http://localhost:3000`);
  open(`http://localhost:3000/login`);
});

const express = require('express');
const path = require('path');
const passport = require('passport');
const SpotifyStrategy = require('passport-spotify').Strategy;
const axios = require('axios');
const session = require('express-session');

const app = express();
const PORT = 3000;

// Your Spotify API credentials
const SPOTIFY_CLIENT_ID = '152cbc5cb3344e44ab7518e6759a9140';
const SPOTIFY_CLIENT_SECRET = 'b3e288faa41e4e3894cedc57c2b05e81';
const REDIRECT_URI = `http://localhost:3000/callback`;

// Passport configuration
passport.use(
    new SpotifyStrategy(
      {
        clientID: SPOTIFY_CLIENT_ID,
        clientSecret: SPOTIFY_CLIENT_SECRET,
        callbackURL: REDIRECT_URI,
      },
      (accessToken, refreshToken, expires_in, profile, done) => {
        // Assuming you want to store the access token in the user object
        const user = {
          accessToken: accessToken,
          // You can also store refreshToken, expires_in, and other user details if needed
        };
        return done(null, user);
      }
    )
  );


passport.serializeUser((user, done) => {
  done(null, user);
});

passport.deserializeUser((user, done) => {
  done(null, user);
});

app.use(session({ secret: 'your-secret-key', resave: true, saveUninitialized: true }));
app.use(passport.initialize());
app.use(passport.session());

app.use(express.static(path.join(__dirname, 'public')));

app.set('view engine', 'ejs');
app.set('views', path.join(__dirname, 'views'));

app.get('/login', passport.authenticate('spotify', { scope: ['user-read-private', 'playlist-read-private'], showDialog: true }));

app.get('/callback', passport.authenticate('spotify', { failureRedirect: '/' }), (req, res) => {
  res.redirect('/dashboard');
});

app.get('/dashboard', isAuthenticated, async (req, res) => {
  try {
    const playlistsResponse = await axios.get('https://api.spotify.com/v1/me/playlists', {
      headers: {
        Authorization: `Bearer ${req.user.accessToken}`,
      },
    });

    const playlists = playlistsResponse.data.items.map(playlist => playlist.name);

    res.render('dashboard', { playlists });
  } catch (error) {
    console.error('Error fetching user playlists:', error.message);
    res.status(500).send('Internal Server Error');
  }
});

function isAuthenticated(req, res, next) {
  if (req.isAuthenticated()) {
    return next();
  } else {
    res.redirect('/login');
  }
}

app.listen(PORT, () => {
  console.log(`Server is running on http://localhost:3000`);
});

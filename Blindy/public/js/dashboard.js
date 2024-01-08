// public/js/dashboard.js
 // Function to retrieve access token from the server
 const getAccessToken = async () => {
    try {
      const response = await fetch('https://accounts.spotify.com/api/token');
      const data = await response.json();
      return data.accessToken;
    } catch (error) {
      console.error('Error retrieving access token:', error);
      return null;
    }
  };


// Initialize the Spotify Web API SDK
window.onSpotifyWebPlaybackSDKReady = async () => {
    const accessToken = await getAccessToken();

    const player = new Spotify.Player({
      name: 'Web Playback SDK',
      getOAuthToken: cb => {
        cb(accessToken);
      },
    });

    // Connect to the Spotify player
    player.connect().then(success => {
      if (success) {
        console.log('The Web Playback SDK is connected!');
      }
    });

    // Function to play a playlist by ID
    const playPlaylist = playlistId => {
      fetch(`https://api.spotify.com/v1/me/player/play`, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
          Authorization: `Bearer ${accessToken}`,
        },
        body: JSON.stringify({
          context_uri: `spotify:playlist:${playlistId}`,
        }),
      })
        .then(response => {
          if (!response.ok) {
            throw new Error('Failed to play playlist');
          }
        })
        .catch(error => console.error('Error playing playlist:', error));
    };

    // Add your Spotify API interaction code here
    fetch('https://api.spotify.com/v1/me/playlists', {
      headers: {
        Authorization: `Bearer ${accessToken}`,
      },
    })
      .then(response => response.json())
      .then(data => {
        console.log('User\'s playlists:', data);
      })
      .catch(error => {
        console.error('Error fetching user\'s playlists:', error);
      });
  };

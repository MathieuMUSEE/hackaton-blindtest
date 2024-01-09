// game.js
const axios = require('axios');

module.exports = {
  createGame,
  assignPlaylist,
  manageTeamConnections,
  toggleRegistration,
  viewRegisteredTeams,
  startGame,
  addTeam,
  getGameCode,
};

const game = {
  code: generateUniqueCode(),
  playlist: null,
  teams: [],
  isOpen: true,
};

function generateUniqueCode() {
  return Math.random().toString(36).substring(7).toUpperCase();
}

function createGame() {
  game.code = generateUniqueCode();
  game.playlist = null;
  game.teams = [];
  game.isOpen = true;
}

async function assignPlaylist(playlistId) {
  try {
    const response = await axios.get(`https://api.spotify.com/v1/playlists/${playlistId}`, {
      headers: {
        Authorization: 'Bearer YOUR_ACCESS_TOKEN', // Replace with your actual Spotify access token
      },
    });

    game.playlist = response.data;
    console.log(`Playlist assigned: ${game.playlist.name}`);
  } catch (error) {
    console.error('Error fetching playlist from Spotify API:', error.message);
  }
}

function manageTeamConnections() {
  // Logic to handle connections of participating devices
}

function toggleRegistration(isOpen) {
  game.isOpen = isOpen;
}

function viewRegisteredTeams() {
  return game.teams;
}

function startGame() {
  // Logic to officially start the game
}

function addTeam(teamName) {
  const team = {
    name: teamName,
    score: 0,
  };
  game.teams.push(team);
}

function getGameCode() {
  return game.code;
}

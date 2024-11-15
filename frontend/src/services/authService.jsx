// src/services/authService.js
import axios from 'axios';

const API_URL = 'http://127.0.0.1:8000/api/v1';

const authService = {
  setAuthHeader: (token) => {
    if (token) {
      axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
    } else {
      delete axios.defaults.headers.common['Authorization'];
    }
  },

  signin: async (credentials) => {
    const response = await axios.post(`${API_URL}/auth/signin`, credentials);
    if (response.data.access_token) {
      localStorage.setItem('token', response.data.access_token);
      authService.setAuthHeader(response.data.access_token);
    }
    return response.data;
  },

  signout: async () => {
    try {
      await axios.post(`${API_URL}/auth/signout`);
    } catch (error) {
      console.error('Signout error:', error);
    } finally {
      localStorage.removeItem('token');
      authService.setAuthHeader(null);
    }
  },

  getCurrentUser: async () => {
    try {
      const response = await axios.get(`${API_URL}/auth/me`);
      return response.data;
    } catch (error) {
      throw error;
    }
  }
};

export default authService;
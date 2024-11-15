// src/pages/Home.jsx

import React from 'react';
import { Link, useNavigate } from 'react-router-dom';
import axios from 'axios';

function Home() {
  const navigate = useNavigate();

  const handleSignOut = async () => {
    try {
      // Lakukan permintaan ke server untuk proses sign out, jika perlu
      await axios.post('/api/v1/auth/signout');
      // Redirect ke halaman sign-in setelah sign out
      navigate('/');
    } catch (error) {
      console.error("Sign out failed:", error);
    }
  };

  return (
    <div>
      <h1>Welcome to Game Platform</h1>
      <p>You are logged in as <strong>{/* tampilkan username user di sini */}</strong></p>
      
      <nav>
        <ul>
          <li><Link to="/games/discover">Discover Games</Link></li>
          <li><Link to="/profile">User Profile</Link></li>
          <li><Link to="/manage-games">Manage Games</Link></li>
          {/* Link khusus admin */}
          <li><Link to="/admin/list-admin">List Admin</Link></li>
          <li><Link to="/admin/list-user">List User</Link></li>
        </ul>
      </nav>

      <button onClick={handleSignOut}>Sign Out</button>
    </div>
  );
}

export default Home;

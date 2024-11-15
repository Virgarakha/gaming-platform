import React from 'react';
import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import SignIn from './pages/SignIn';
import SignUp from './pages/SignUp';
import Home from './pages/Home';
import DiscoverGames from './pages/DiscoverGames';
// impor halaman lain sesuai kebutuhan...

function App() {
  return (
    <Router>
      <Routes>
        <Route path="/" element={<SignIn />} />
        <Route path="/signup" element={<SignUp />} />
        <Route path="/home" element={<Home />} />
        <Route path="/games/discover" element={<DiscoverGames />} />
        {/* tambahkan rute halaman lainnya */}
      </Routes>
    </Router>
  );
}

export default App;

import React, { useState, useEffect } from 'react';
import axios from 'axios';

function DiscoverGames() {
  const [games, setGames] = useState([]);
  const [page, setPage] = useState(1);

  useEffect(() => {
    const fetchGames = async () => {
      const response = await axios.get(`/api/v1/games?page=${page}`);
      setGames((prev) => [...prev, ...response.data]);
    };
    fetchGames();
  }, [page]);

  const handleScroll = (e) => {
    if (e.target.documentElement.scrollHeight - window.innerHeight - window.scrollY < 100) {
      setPage((prev) => prev + 1);
    }
  };

  useEffect(() => {
    window.addEventListener('scroll', handleScroll);
    return () => window.removeEventListener('scroll', handleScroll);
  }, []);

  return (
    <div>
      {games.map((game) => (
        <div key={game.slug}>
          <h3>{game.title}</h3>
          <p>{game.description}</p>
        </div>
      ))}
    </div>
  );
}

export default DiscoverGames;

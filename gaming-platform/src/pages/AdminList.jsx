import React, { useEffect, useState } from 'react';
import axios from 'axios';

function AdminList() {
  const [admins, setAdmins] = useState([]);

  useEffect(() => {
    const fetchAdmins = async () => {
      const response = await axios.get('/api/v1/admins');
      setAdmins(response.data);
    };
    fetchAdmins();
  }, []);

  return (
    <div>
      <h2>Admin List</h2>
      <ul>
        {admins.map((admin) => (
          <li key={admin.username}>
            {admin.username} - Registered on: {new Date(admin.registeredTimestamp).toLocaleDateString()}
          </li>
        ))}
      </ul>
    </div>
  );
}

export default AdminList;

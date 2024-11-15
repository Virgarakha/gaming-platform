import React from 'react';
import { useForm } from 'react-hook-form';
import { useNavigate } from 'react-router-dom';
import useUserStore from '../store/userStore';
import axios from 'axios';

function SignIn() {
  const { register, handleSubmit, formState: { errors } } = useForm();
  const navigate = useNavigate();
  const setUser = useUserStore((state) => state.setUser);

  const onSubmit = async (data) => {
    try {
      const response = await axios.post('/api/v1/auth/signup', data);
      setUser(response.data.user);
      navigate('/home');
    } catch (error) {
      console.error("Login failed", error);
      alert("Login failed");
    }
  };

  return (
    <form onSubmit={handleSubmit(onSubmit)}>
      <label>Username</label>
      <input {...register("username", { required: true })} />
      {errors.username && <span>Username is required</span>}
      
      <label>Password</label>
      <input type="password" {...register("password", { required: true })} />
      {errors.password && <span>Password is required</span>}
      
      <button type="submit">Sign In</button>
    </form>
  );
}

export default SignIn;

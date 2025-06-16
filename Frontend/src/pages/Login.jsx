import React, { useState } from 'react';
import { useHistory } from 'react-router-dom';
import { useUser } from '../contexts/UserContext';
import axios from 'axios';

export default function LoginPage() {
  const { login } = useUser();
  const history = useHistory();
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [error, setError] = useState('');

  const handleSubmit = async (e) => {
    e.preventDefault();
    setError('');

    try {
      const res = await axios.post('http://localhost:8080/users/auth', {
        email,
        password,
      });

      // supondo que o backend retorne { user, token } em status 200
      const { user, token } = res.data;
      login(user, token); 
      // opcional: salvar token/localStorage
      // localStorage.setItem('token', token);

      // redireciona somente se deu certo
      history.push('/home');
    } catch (err) {
      // Axios coloca o erro da resposta em err.response
      if (err.response) {
        // Mensagem que seu backend enviar, ex: { message: "Credenciais inválidas" }
        setError(err.response.data.message || 'Falha no login');
      } else {
        // erro de rede ou outro
        setError('Erro de conexão. Tente novamente.');
      }
    }
  };

  return (
    <div className="max-w-md mx-auto mt-20 p-6 shadow-lg rounded-lg">
      <h1 className="text-2xl mb-4">Login</h1>
      {error && <div className="mb-4 text-red-600">{error}</div>}
      <form onSubmit={handleSubmit} className="space-y-4">
        <div>
          <label className="block mb-1">Email</label>
          <input
            type="email"
            className="w-full p-2 border rounded"
            value={email}
            onChange={e => setEmail(e.target.value)}
            required
          />
        </div>
        <div>
          <label className="block mb-1">Senha</label>
          <input
            type="password"
            className="w-full p-2 border rounded"
            value={password}
            onChange={e => setPassword(e.target.value)}
            required
          />
        </div>
        <button
          type="submit"
          className="w-full py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
        >
          Entrar
        </button>
      </form>
    </div>
  );
}

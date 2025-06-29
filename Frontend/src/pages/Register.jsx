import React from 'react';
import { useState } from 'react';
import { useHistory, Link } from 'react-router-dom'; 
import apiClient from '../api/axiosConfig';

export default function Register() {
  const [form, setForm] = useState({
    name: '',
    email: '',
    password: '',
    about: '',
    is_private: false,
  });
  const [error, setError] = useState(null);
  const [loading, setLoading] = useState(false); 
  const history = useHistory();

  const handleChange = (field, value) => {
    setForm((prev) => ({ ...prev, [field]: value }));
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setError(null);
    setLoading(true);
    try {
      await apiClient.post('/users', form);
      alert('Conta criada com sucesso! Por favor, faça o login.');
      history.push('/login');
    } catch (err) {
      console.error(err);
      setError(err.response?.data?.messages?.error || 'Erro desconhecido ao criar conta.');
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="max-w-md mx-auto mt-20 p-6 shadow-lg rounded-lg bg-white">
      <h1 className="text-2xl font-bold mb-4 text-center">Cadastrar-se</h1>
      
      {error && (
        <div className="mb-4 text-red-600 bg-red-100 p-3 rounded-md">
          {error}
        </div>
      )}

      <form onSubmit={handleSubmit} className="space-y-4">
        <div>
          <label className="block mb-1 font-medium">Nome de Usuário</label>
          <input
            type="text"
            required
            className="w-full p-2 border rounded"
            value={form.name}
            onChange={(e) => handleChange('name', e.target.value)}
          />
        </div>

        <div>
          <label className="block mb-1 font-medium">Email</label>
          <input
            type="email"
            required
            className="w-full p-2 border rounded"
            value={form.email}
            onChange={(e) => handleChange('email', e.target.value)}
          />
        </div>

        <div>
          <label className="block mb-1 font-medium">Senha</label>
          <input
            type="password"
            required
            className="w-full p-2 border rounded"
            value={form.password}
            onChange={(e) => handleChange('password', e.target.value)}
          />
        </div>
        
        <div>
          <label className="block mb-1 font-medium">Sobre você (Opcional)</label>
          <textarea
            placeholder="Conte um pouco sobre você..."
            className="w-full p-2 border rounded"
            value={form.about}
            onChange={(e) => handleChange('about', e.target.value)}
          />
        </div>
        
        <div className="flex items-center">
          <input
            id="private-check"
            type="checkbox"
            checked={form.is_private}
            onChange={(e) => handleChange('is_private', e.target.checked)}
            className="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
          />
          <label htmlFor="private-check" className="ml-2 block text-sm text-gray-900">
            Tornar perfil privado?
          </label>
        </div>

        <button
          type="submit"
          disabled={loading}
          className="w-full py-2 bg-blue-600 text-white rounded hover:bg-blue-700 font-semibold disabled:bg-gray-400"
        >
          {loading ? 'Criando...' : 'Criar Conta'}
        </button>
      </form>
      
      <p className="text-center text-sm mt-4">
        Já tem uma conta?{' '}
        <Link to="/login" className="text-blue-600 hover:underline font-semibold">
          Faça o login
        </Link>
      </p>
    </div>
  );
}
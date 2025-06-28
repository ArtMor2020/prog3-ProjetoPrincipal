import React, { useState } from 'react';
import { useHistory } from 'react-router-dom';
import { useUser } from '../contexts/UserContext';
import Header from '../components/Header';
import apiClient from '../api/axiosConfig'; 

export default function CommunityCreatePage() {
  const { user } = useUser();
  const history = useHistory();

  const [name, setName] = useState('');
  const [description, setDescription] = useState('');
  const [isPrivate, setIsPrivate] = useState(false);
  const [error, setError] = useState(null);
  const [loading, setLoading] = useState(false);

  const handleSubmit = async (e) => {
    e.preventDefault();
    setError(null);
    setLoading(true);

    if (!name.trim() || !description.trim()) {
      setError('Nome e descrição são obrigatórios.');
      setLoading(false);
      return;
    }

    try {
      // --- CORREÇÃO: Usa apiClient ---
      const { data } = await apiClient.post('/communities', {
        name,
        description,
        id_owner: user.id,
        is_private: isPrivate,
      });
      history.push(`/communities/${data.id}`);
    } catch (err) {
      console.error('Erro ao criar comunidade:', err);
      setError(err.response?.data?.message || 'Não foi possível criar a comunidade.');
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="min-h-screen bg-gray-50">
      <Header />

      <div className="max-w-md mx-auto mt-12 bg-white p-8 rounded-lg shadow-md">
        <h1 className="text-2xl font-semibold mb-6 text-center">Criar Nova Comunidade</h1>

        {error && (
          <div className="mb-4 text-red-600 bg-red-100 p-3 rounded-md">
            {error}
          </div>
        )}

        <form onSubmit={handleSubmit} className="space-y-4">
          <div>
            <label className="block mb-1 font-medium">Nome da Comunidade</label>
            <input
              type="text"
              value={name}
              onChange={e => setName(e.target.value)}
              className="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="Ex: r/Futebol"
              required
            />
          </div>

          <div>
            <label className="block mb-1 font-medium">Descrição</label>
            <textarea
              value={description}
              onChange={e => setDescription(e.target.value)}
              className="w-full border rounded px-3 py-2 h-24 focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="Descreva o propósito da sua comunidade."
              required
            />
          </div>

          <div className="flex items-center">
            <input
              id="private"
              type="checkbox"
              checked={isPrivate}
              onChange={e => setIsPrivate(e.target.checked)}
              className="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
            />
            <label htmlFor="private" className="ml-2 block text-sm text-gray-900">
              Comunidade privada (requer aprovação para entrar)
            </label>
          </div>

          <button
            type="submit"
            disabled={loading}
            className="w-full bg-blue-600 text-white py-2 rounded-md font-semibold hover:bg-blue-700 transition disabled:bg-gray-400"
          >
            {loading ? 'Criando...' : 'Criar Comunidade'}
          </button>
        </form>
      </div>
    </div>
  );
}
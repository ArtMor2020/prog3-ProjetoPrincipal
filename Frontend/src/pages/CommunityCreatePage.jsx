// src/pages/CommunityCreatePage.jsx
import React, { useState } from 'react';
import { useHistory } from 'react-router-dom';
import axios from 'axios';
import Header from '../components/Header';
import { useUser } from '../contexts/UserContext';

export default function CommunityCreatePage() {
  const { user } = useUser();
  const history = useHistory();

  const [name, setName] = useState('');
  const [description, setDescription] = useState('');
  const [isPrivate, setIsPrivate] = useState(false);
  const [error, setError] = useState(null);

  const handleSubmit = async (e) => {
    e.preventDefault();

    if (!name.trim() || !description.trim()) {
      setError('Nome e descrição são obrigatórios.');
      return;
    }

    try {
      const { data } = await axios.post('http://localhost:8080/communities', {
        name,
        description,
        id_owner: user.id,
        is_private: isPrivate,
      });
      history.push(`/communities/${data.id}`);
    } catch (err) {
      console.error('Erro ao criar comunidade:', err);
      setError('Não foi possível criar a comunidade.');
    }
  };

  return (
    <div className="min-h-screen bg-gray-50">
      <Header onSearch={() => {}} />

      <div className="max-w-md mx-auto mt-12 bg-white p-8 rounded shadow">
        <h1 className="text-2xl font-semibold mb-6">Criar Comunidade</h1>

        {error && (
          <div className="mb-4 text-red-600">
            {error}
          </div>
        )}

        <form onSubmit={handleSubmit} className="space-y-4">
          <div>
            <label className="block mb-1 font-medium">Nome</label>
            <input
              type="text"
              value={name}
              onChange={e => setName(e.target.value)}
              className="w-full border rounded px-3 py-2 focus:outline-none focus:ring"
              placeholder="Ex: Brasil Dev"
            />
          </div>

          <div>
            <label className="block mb-1 font-medium">Descrição</label>
            <textarea
              value={description}
              onChange={e => setDescription(e.target.value)}
              className="w-full border rounded px-3 py-2 h-24 focus:outline-none focus:ring"
              placeholder="Comunidade de desenvolvedores brasileiros"
            />
          </div>

          <div className="flex items-center">
            <input
              id="private"
              type="checkbox"
              checked={isPrivate}
              onChange={e => setIsPrivate(e.target.checked)}
              className="mr-2"
            />
            <label htmlFor="private" className="font-medium">
              Comunidade privada
            </label>
          </div>

          <button
            type="submit"
            className="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-500 transition"
          >
            Criar
          </button>
        </form>
      </div>
    </div>
  );
}

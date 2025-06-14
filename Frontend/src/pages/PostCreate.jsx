// src/pages/PostCreate.jsx
import React, { useEffect, useState } from 'react';
import axios from 'axios';
import { useHistory } from 'react-router-dom';
import { useUser } from '../contexts/UserContext';
import Header from '../components/Header';

export default function PostCreate() {
  const { user } = useUser();
  const history = useHistory();

  const [communities, setCommunities] = useState([]);
  const [form, setForm] = useState({
    id_community: '',
    title: '',
    description: '',
  });
  const [error, setError] = useState('');

  // Carrega lista de comunidades
  useEffect(() => {
    axios.get('http://localhost:8080/communities')
  .then(({ data }) => {
   // filtra apenas nomes únicos
    const unique = data.filter((c, i, arr) =>
     arr.findIndex(x => x.name === c.name) === i
    );
    setCommunities(unique);
   })
  .catch(err => console.error('Erro ao carregar comunidades:', err));
  }, []);

  const handleChange = (e) => {
    setForm({ ...form, [e.target.name]: e.target.value });
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setError('');

    // Validação básica
    if (!form.id_community || !form.title.trim() || !form.description.trim()) {
      setError('Todos os campos são obrigatórios.');
      return;
    }

    try {
      const payload = {
        id_user: user.id,
        id_community: Number(form.id_community),
        title: form.title.trim(),
        description: form.description.trim(),
      };
      const res = await axios.post('http://localhost:8080/posts', payload);
      // res.data.id é o novo post
      history.push(`/posts/${res.data.id}`);
    } catch (err) {
      console.error('Erro ao criar post:', err);
      setError('Não foi possível criar o post. Tente novamente.');
    }
  };

  return (
    <div>
      <Header onSearch={() => {}} />

      <div className="max-w-xl mx-auto mt-8 p-6 bg-white rounded-lg shadow">
        <h1 className="text-2xl font-semibold mb-4">Criar Novo Post</h1>

        {error && (
          <div className="mb-4 p-2 bg-red-100 text-red-800 rounded">
            {error}
          </div>
        )}

        <form onSubmit={handleSubmit} className="space-y-4">
          {/* Comunidade */}
          <div>
            <label className="block mb-1 font-medium">Comunidade</label>
            <select
              name="id_community"
              value={form.id_community}
              onChange={handleChange}
              className="w-full border rounded px-3 py-2"
            >
              <option value="">Selecione...</option>
              {communities.map(c => (
                <option key={c.id} value={c.id}>
                  {c.name}
                </option>
              ))}
            </select>
          </div>

          {/* Título */}
          <div>
            <label className="block mb-1 font-medium">Título</label>
            <input
              type="text"
              name="title"
              value={form.title}
              onChange={handleChange}
              className="w-full border rounded px-3 py-2"
              placeholder="Digite o título do post"
            />
          </div>

          {/* Descrição */}
          <div>
            <label className="block mb-1 font-medium">Descrição</label>
            <textarea
              name="description"
              value={form.description}
              onChange={handleChange}
              rows="6"
              className="w-full border rounded px-3 py-2"
              placeholder="Digite o conteúdo completo do post"
            />
          </div>

          {/* Botão */}
          <div className="text-right">
            <button
              type="submit"
              className="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-500"
            >
              Publicar
            </button>
          </div>
        </form>
      </div>
    </div>
  );
}

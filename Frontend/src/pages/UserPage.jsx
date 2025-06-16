// src/pages/UserPage.jsx
import React, { useEffect, useState } from 'react';
import { useParams } from 'react-router-dom';
import axios from 'axios';
import Header from '../components/Header';
import PostCard from '../components/PostCard';

export default function UserPage() {
  const { userId } = useParams();
  const [user, setUser]     = useState(null);
  const [posts, setPosts]   = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchUserAndPosts = async () => {
      try {
        // 1) Buscar dados do usuário
        const userRes = await axios.get(`http://localhost:8080/users/${userId}`);
        setUser(userRes.data);

        // 2) Buscar todos os posts
        const postsRes = await axios.get('http://localhost:8080/posts');
        const allPosts = postsRes.data;

        // 3) Filtrar apenas os posts deste usuário e não deletados
        const userPosts = allPosts.filter(
          (p) => String(p.id_user) === String(userId) && p.is_deleted === "0"
        );

        // 4) Enriquecer cada post (autor é o próprio user, mas buscamos comunidade e comentários)
        const enriched = await Promise.all(
          userPosts.map(async (post) => {
            const [commRes, commentsRes] = await Promise.all([
              axios.get(`http://localhost:8080/communities/${post.id_community}`),
              axios.get(`http://localhost:8080/comments/post/${post.id}`),
            ]);
            return {
              id: post.id,
              id_user: post.id_user,
              id_community: post.id_community,
              title:         post.title,
              score:         0, // subistitua pela rota de score se desejar
              submittedAt:   post.posted_at,
              author:        userRes.data.name,
              community:     commRes.data.name,
              commentsCount: Array.isArray(commentsRes.data)
                ? commentsRes.data.length
                : 0,
            };
          })
        );

        setPosts(enriched);
      } catch (err) {
        console.error('Erro ao carregar perfil e posts:', err);
      } finally {
        setLoading(false);
      }
    };

    fetchUserAndPosts();
  }, [userId]);

  if (loading) {
    return <div className="text-center mt-10">Carregando...</div>;
  }

  if (!user) {
    return <div className="text-center mt-10">Usuário não encontrado.</div>;
  }

  return (
    <div>
      <Header onSearch={() => {}} />
    <div className="max-w-2xl mx-auto mt-6 space-y-6">

      {/* Seção de perfil do usuário */}
      <div className="bg-white shadow-sm rounded p-6">
        <h1 className="text-2xl font-bold">{user.name}</h1>
        {user.about && (
          <p className="mt-2 text-gray-700">{user.about}</p>
        )}
      </div>

      {/* Lista de posts do usuário */}
      <div className="space-y-4">
        <h2 className="text-xl font-semibold">Posts de {user.name}</h2>
        {posts.length > 0 ? (
          posts.map((post) => (
            <PostCard
              key={post.id}
              post={post}
              currentUserId={user.id}
            />
          ))
        ) : (
          <p className="text-gray-600">Este usuário ainda não criou nenhum post.</p>
        )}
      </div>
    </div>
    </div>
  );
}

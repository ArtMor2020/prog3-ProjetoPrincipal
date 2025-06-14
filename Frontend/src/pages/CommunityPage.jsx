// src/pages/CommunityPage.jsx
import React, { useEffect, useState } from 'react';
import { useParams } from 'react-router-dom';
import axios from 'axios';
import Header from '../components/Header';
import PostCard from '../components/PostCard';

export default function CommunityPage() {
  const { communityId } = useParams();
  const [community, setCommunity] = useState(null);
  const [posts, setPosts]         = useState([]);
  const [loading, setLoading]     = useState(true);

  useEffect(() => {
    const fetchCommunityAndPosts = async () => {
      try {
        // 1) Buscar dados da comunidade
        const commRes = await axios.get(`http://localhost:8080/communities/${communityId}`);
        setCommunity(commRes.data);

        // 2) Buscar todos os posts
        const postsRes = await axios.get('http://localhost:8080/posts');
        const allPosts = postsRes.data;

        // 3) Filtrar apenas os posts desta comunidade não deletados
        const commPosts = allPosts.filter(
          (p) => String(p.id_community) === String(communityId) && p.is_deleted === "0"
        );

        // 4) Enriquecer cada post com autor, comentários etc.
        const enriched = await Promise.all(
          commPosts.map(async (post) => {
            const [userRes, commentsRes] = await Promise.all([
              axios.get(`http://localhost:8080/users/${post.id_user}`),
              axios.get(`http://localhost:8080/comments/post/${post.id}`),
            ]);
            return {
              id: post.id,
              id_user: post.id_user,
              id_community: post.id_community,
              title:         post.title,
              score:         0, // ou buscar via rota de score
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
        console.error('Erro ao carregar comunidade e posts:', err);
      } finally {
        setLoading(false);
      }
    };

    fetchCommunityAndPosts();
  }, [communityId]);

  if (loading) {
    return <div className="text-center mt-10">Carregando...</div>;
  }

  if (!community) {
    return <div className="text-center mt-10">Comunidade não encontrada.</div>;
  }

  return (
    <div>
      <Header onSearch={() => {}} />
    <div className="max-w-2xl mx-auto mt-6 space-y-6">

      {/* Seção de info da comunidade */}
      <div className="bg-white shadow-sm rounded p-6">
        <h1 className="text-2xl font-bold">r/{community.name}</h1>
        {community.description && (
          <p className="mt-2 text-gray-700">{community.description}</p>
        )}
      </div>

      {/* Lista de posts da comunidade */}
      <div className="space-y-4">
        <h2 className="text-xl font-semibold">Posts em r/{community.name}</h2>
        {posts.length > 0 ? (
          posts.map((post) => (
            <PostCard
              key={post.id}
              post={post}
              currentUserId={post.id_community}
            />
          ))
        ) : (
          <p className="text-gray-600">Nenhum post nesta comunidade ainda.</p>
        )}
      </div>
    </div>
    </div>

  );
}

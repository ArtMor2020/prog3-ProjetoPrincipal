// src/pages/HomePage.jsx
import React, { useEffect, useState } from 'react';
import axios from 'axios';
import PostCard from '../components/PostCard';
import Header from '../components/Header';
import { useUser } from '../contexts/UserContext';

export default function HomePage() {
  const { user } = useUser();
  const [posts, setPosts] = useState([]);

  // Paginação
  const [page, setPage] = useState(1);
  const postsPerPage = 10;
  const totalPages = Math.ceil(posts.length / postsPerPage);

  useEffect(() => {
    const fetchPosts = async () => {
      try {
        const { data } = await axios.get('http://localhost:8080/posts');
        const enriched = await Promise.all(
          data.map(async (post) => {
            if (post.is_deleted === "1") return null;

            const [userRes, commRes, commentsRes] = await Promise.all([
              axios.get(`http://localhost:8080/users/${post.id_user}`),
              axios.get(`http://localhost:8080/communities/${post.id_community}`),
              axios.get(`http://localhost:8080/comments/post/${post.id}`),
            ]);

            return {
              id: post.id,
              id_user: post.id_user,
              id_community: post.id_community,
              title:         post.title,
              score:         0,
              submittedAt:   post.posted_at,
              author:        userRes.data.name,
              community:     commRes.data.name,
              commentsCount: Array.isArray(commentsRes.data) ? commentsRes.data.length : 0,
            };
          })
        );
        setPosts(enriched.filter(p => p !== null));
      } catch (err) {
        console.error('Erro ao carregar posts:', err);
      }
    };

    fetchPosts();
  }, []);

  if (!user) return <div>Carregando...</div>;

  // calcular slice da página atual
  const start = (page - 1) * postsPerPage;
  const currentPosts = posts.slice(start, start + postsPerPage);

  return (
    <div className="max-w-2xl mx-auto mt-6 space-y-6">
      <Header onSearch={() => {}} />

      {currentPosts.map(post => (
        <PostCard
          key={post.id}
          post={post}
          currentUserId={user.id}
        />
      ))}

      {/* Paginação */}
      <div className="flex flex-col items-center py-4">
        <span className="mb-2 text-gray-600 font-medium">Páginas</span>
        <div className="flex items-center space-x-4">
          <button
            onClick={() => setPage(p => Math.max(1, p - 1))}
            disabled={page === 1}
            className="p-2 rounded-full bg-gray-200 disabled:opacity-50 hover:bg-gray-300"
          >
            ←
          </button>
          <div className="w-8 h-8 flex items-center justify-center rounded-full bg-blue-500 text-white font-bold">
            {page}
          </div>
          <button
            onClick={() => setPage(p => Math.min(totalPages, p + 1))}
            disabled={page === totalPages}
            className="p-2 rounded-full bg-gray-200 disabled:opacity-50 hover:bg-gray-300"
          >
            →
          </button>
        </div>
      </div>
    </div>
  );
}

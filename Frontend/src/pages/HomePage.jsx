import React, { useState, useEffect, useCallback } from 'react';
import { useUser } from '../contexts/UserContext';
import Header from '../components/Header';
import PostCard from '../components/PostCard';
import EditPostModal from '../components/EditPostModal';
import apiClient from '../api/axiosConfig';

export default function HomePage() {
  const { user } = useUser();
  const [posts, setPosts] = useState([]);
  const [loading, setLoading] = useState(true);
  const [page, setPage] = useState(1);
  const postsPerPage = 10;
  const [isEditModalOpen, setIsEditModalOpen] = useState(false);
  const [editingPost, setEditingPost] = useState(null);

  const fetchPosts = useCallback(async () => {
    if (!user) { setLoading(false); return; }
    setLoading(true);
    try {
      const { data } = await apiClient.get(`/posts?viewerId=${user.id}`);
      const enriched = await Promise.all(
        data.map(async (post) => {
          try {
            const userRes = await apiClient.get(`/users/${post.id_user}`);
            let communityName = userRes.data.name;
            if (post.id_community) {
              const commRes = await apiClient.get(`/communities/${post.id_community}`);
              communityName = commRes.data.name;
            }
            const commentsRes = await apiClient.get(`/comments/post/${post.id}?viewerId=${user.id}`);
            return {
              ...post, author: userRes.data.name, community: communityName, isProfilePost: !post.id_community,
              commentsCount: Array.isArray(commentsRes.data) ? commentsRes.data.length : 0,
            };
          } catch (error) { return null; }
        })
      );
      setPosts(enriched.filter(p => p !== null));
    } catch (err) {
      console.error('Erro ao carregar posts:', err);
    } finally {
      setLoading(false);
    }
  }, [user]);

  useEffect(() => { fetchPosts(); }, [fetchPosts]);
  
  const handleEditRequest = (post) => { setEditingPost(post); setIsEditModalOpen(true); };
  const handleDeleteRequest = async (postId) => {
    if (window.confirm("Tem certeza?")) { await apiClient.delete(`/posts/${postId}`); fetchPosts(); }
  };
  const handleReportRequest = async (postId) => { await apiClient.post(`/posts/${postId}/report`); alert('Post reportado.'); };
  const handleSaveEdit = async (postId, updatedData) => {
    await apiClient.put(`/posts/${postId}`, updatedData);
    setIsEditModalOpen(false);
    fetchPosts();
  };

  if (loading) return <div><Header /><div className="text-center mt-10">Carregando...</div></div>;
  const totalPages = Math.ceil(posts.length / postsPerPage);
  const currentPosts = posts.slice((page - 1) * postsPerPage, page * postsPerPage);

  return (
    <div>
      <Header />
      <div className="max-w-2xl mx-auto mt-6 space-y-6">
        {currentPosts.length > 0 ? currentPosts.map(post => (
          <PostCard key={post.id} post={post} currentUserId={user.id} onEditRequest={handleEditRequest} onDeleteRequest={handleDeleteRequest} onReportRequest={handleReportRequest} />
        )) : <div className="text-center text-gray-500 py-10">Nenhum post.</div>}
        {totalPages > 1 && (
          <div className="flex justify-center items-center space-x-4 py-4">
            <button onClick={() => setPage(p => Math.max(1, p - 1))} disabled={page === 1}>Anterior</button>
            <span>Página {page} de {totalPages}</span>
            <button onClick={() => setPage(p => Math.min(totalPages, p + 1))} disabled={page === totalPages}>Próxima</button>
          </div>
        )}
      </div>
      <EditPostModal post={editingPost} isOpen={isEditModalOpen} onClose={() => setIsEditModalOpen(false)} onSave={handleSaveEdit} />
    </div>
  );
}
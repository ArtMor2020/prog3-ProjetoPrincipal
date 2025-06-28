import React, { useEffect, useState, useCallback } from 'react';
import { useParams } from 'react-router-dom';
import apiClient from '../api/axiosConfig';
import Header from '../components/Header';
import PostCard from '../components/PostCard';
import { useUser } from '../contexts/UserContext';
import CommunityJoinButton from '../components/CommunityJoinButton';
import JoinRequestManager from '../components/JoinRequestManager';
import EditPostModal from '../components/EditPostModal';

export default function CommunityPage() {
  const { communityId } = useParams();
  const { user } = useUser();
  const [community, setCommunity] = useState(null);
  const [posts, setPosts] = useState([]);
  const [loading, setLoading] = useState(true);
  const [isModerator, setIsModerator] = useState(false);
  const [isEditModalOpen, setIsEditModalOpen] = useState(false);
  const [editingPost, setEditingPost] = useState(null);

  const fetchPageData = useCallback(async () => {
    if (!user) return;
    setLoading(true);
    try {
      const [commRes, statusRes] = await Promise.all([
        apiClient.get(`/communities/${communityId}`),
        apiClient.get(`/communities/user-status/${communityId}/${user.id}`)
      ]);
      setCommunity(commRes.data);
      if (['ADMIN', 'MODERATOR'].includes(statusRes.data.status)) {
        setIsModerator(true);
      }

      const postsRes = await apiClient.get(`/posts?viewerId=${user.id}`);
      const communityPosts = postsRes.data.filter(p => String(p.id_community) === String(communityId));

      const enriched = await Promise.all(
        communityPosts.map(async (post) => {
          try {
            const userRes = await apiClient.get(`/users/${post.id_user}`);
            const commentsRes = await apiClient.get(`/comments/post/${post.id}?viewerId=${user.id}`);
            return {
              ...post,
              author: userRes.data.name,
              community: commRes.data.name,
              isProfilePost: !post.id_community,
              commentsCount: Array.isArray(commentsRes.data) ? commentsRes.data.length : 0,
            };
          } catch (err) { return null; }
        })
      );
      setPosts(enriched.filter(p => p !== null));

    } catch (err) {
      console.error('Erro ao carregar dados da página da comunidade:', err);
    } finally {
      setLoading(false);
    }
  }, [communityId, user]);

  useEffect(() => {
    fetchPageData();
  }, [fetchPageData]);

  const handleAction = () => {
    fetchPageData();
  };
  
  const handleEditRequest = (post) => { setEditingPost(post); setIsEditModalOpen(true); };
  const handleDeleteRequest = async (postId) => { if (window.confirm("Tem certeza?")) { await apiClient.delete(`/posts/${postId}`); fetchPageData(); }};
  const handleReportRequest = async (postId) => { await apiClient.post(`/posts/${postId}/report`); alert('Post reportado.'); };
  const handleSaveEdit = async (postId, updatedData) => { await apiClient.put(`/posts/${postId}`, updatedData); setIsEditModalOpen(false); fetchPageData(); };

  if (loading) return <div><Header /><div className="text-center mt-10">Carregando...</div></div>;
  if (!community) return <div><Header /><div className="text-center mt-10">Comunidade não encontrada.</div></div>;

  return (
    <div>
      <Header />
      <div className="max-w-2xl mx-auto mt-6 space-y-6">
        <div className="bg-white shadow-sm rounded p-6">
          <div className="flex items-center justify-between">
            <h1 className="text-2xl font-bold">r/{community.name}</h1>
            <CommunityJoinButton communityId={parseInt(communityId, 10)} />
          </div>
          {community.description && <p className="mt-2 text-gray-700">{community.description}</p>}
        </div>

        <JoinRequestManager communityId={communityId} isModerator={isModerator} />
        
        <div className="space-y-4">
          <h2 className="text-xl font-semibold">Posts em r/{community.name}</h2>
          {posts.length > 0 ? (
            posts.map((post) => (
              <PostCard 
                key={post.id} 
                post={post} 
                currentUserId={user?.id}
                onEditRequest={handleEditRequest}
                onDeleteRequest={handleDeleteRequest}
                onReportRequest={handleReportRequest}
              />
            ))
          ) : (
            <p className="text-gray-600 p-4 text-center">Nenhum post nesta comunidade ainda.</p>
          )}
        </div>
      </div>
      <EditPostModal post={editingPost} isOpen={isEditModalOpen} onClose={() => setIsEditModalOpen(false)} onSave={handleSaveEdit} />
    </div>
  );
}
import React, { useEffect, useState, useCallback } from 'react';
import { useParams, useHistory } from 'react-router-dom';
import { useUser } from '../contexts/UserContext';
import PostVoteHeader from '../components/PostVoteHeader';
import CommentCard from '../components/CommentCard';
import Header from '../components/Header';
import MentionTextarea from '../components/MentionTextarea';
import EditPostModal from '../components/EditPostModal';
import { Download } from 'lucide-react';
import apiClient from '../api/axiosConfig';

const AttachmentDisplay = ({ attachment }) => {
  const attachmentUrl = `${apiClient.defaults.baseURL}/attachments/serve/${attachment.id}`;

  if (attachment.type === 'IMAGE') {
    return <img src={attachmentUrl} alt="Anexo do post" className="max-w-full rounded-lg my-4" />;
  }
  if (attachment.type === 'VIDEO') {
    return <video src={attachmentUrl} controls className="max-w-full rounded-lg my-4" />;
  }
  return (
    <a 
      href={attachmentUrl} 
      target="_blank" 
      rel="noopener noreferrer" 
      className="flex items-center p-3 my-2 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors"
    >
      <Download size={20} className="mr-3 text-gray-600" />
      <span>Baixar anexo ({attachment.type.toLowerCase()})</span>
    </a>
  );
};

export default function PostPage() {
  const { postId } = useParams();
  const { user } = useUser();
  const history = useHistory();

  const [post, setPost] = useState(null);
  const [attachments, setAttachments] = useState([]);
  const [commentsTree, setCommentsTree] = useState([]);
  const [newComment, setNewComment] = useState('');
  const [loading, setLoading] = useState(true);

  const [isEditModalOpen, setIsEditModalOpen] = useState(false);
  const [editingPost, setEditingPost] = useState(null);

  const loadPostData = useCallback(async () => {
    try {
      const postRes = await apiClient.get(`/posts/${postId}`);
      const [postData, attachmentsData] = postRes.data;
      
      if (!postData) {
        setPost(null);
        return;
      }

      const userRes = await apiClient.get(`/users/${postData.id_user}`);
      let communityName = userRes.data.name;

      if (postData.id_community) {
        const commRes = await apiClient.get(`/communities/${postData.id_community}`);
        communityName = commRes.data.name;
      }
      
      setPost({
        ...postData,
        userName: userRes.data.name,
        communityName,
        isProfilePost: !postData.id_community,
      });
      setAttachments(attachmentsData);
      
    } catch (err) {
      console.error('Erro ao carregar post:', err);
      setPost(null);
    }
  }, [postId]);

  const loadComments = useCallback(async () => {
    if (!user) return;
    try {
      const { data } = await apiClient.get(`/comments/post/${postId}?viewerId=${user.id}`);
      const alive = data.filter(c => c.is_deleted === "0");
      if (alive.length === 0) {
        setCommentsTree([]);
        return;
      }
      const byId = {};
      alive.forEach(c => byId[c.id] = { ...c, replies: [] });
      const userPromises = alive.map(c => apiClient.get(`/users/${c.id_user}`));
      const userResponses = await Promise.all(userPromises);
      userResponses.forEach((res, index) => {
        const commentId = alive[index].id;
        if (byId[commentId]) {
          byId[commentId].userName = res.data.name;
        }
      });
      const tree = [];
      Object.values(byId).forEach(c => {
        if (c.id_parent_comment && byId[c.id_parent_comment]) {
          byId[c.id_parent_comment].replies.push(c);
        } else {
          tree.push(c);
        }
      });
      setCommentsTree(tree);
    } catch (err) {
      console.error('Erro ao carregar comentários:', err);
    }
  }, [postId, user]);

  useEffect(() => {
    const loadAllData = async () => {
      setLoading(true);
      await loadPostData();
      await loadComments();
      setLoading(false);
    };
    loadAllData();
  }, [postId, loadPostData, loadComments]);

  const handleEditRequest = (postToEdit) => {
    setEditingPost(postToEdit);
    setIsEditModalOpen(true);
  };

  const handleDeleteRequest = async (postIdToDelete) => {
    if (window.confirm("Tem certeza que deseja excluir este post?")) {
      try {
        await apiClient.delete(`/posts/${postIdToDelete}`);
        history.push('/home');
      } catch (error) {
        console.error("Erro ao excluir post:", error);
        alert("Não foi possível excluir o post.");
      }
    }
  };
  
  const handleReportRequest = async (postIdToReport) => {
    try {
      await apiClient.post(`/posts/${postIdToReport}/report`);
      alert('Post reportado com sucesso! A moderação irá analisar.');
    } catch (error) {
      console.error("Erro ao reportar post:", error);
      alert("Não foi possível reportar o post.");
    }
  };

  const handleSaveEdit = async (postIdToSave, updatedData) => {
    try {
      await apiClient.put(`/posts/${postIdToSave}`, updatedData);
      setIsEditModalOpen(false);
      setEditingPost(null);
      loadPostData();
    } catch (error) {
      console.error("Erro ao salvar edição do post:", error);
      alert("Não foi possível salvar as alterações.");
    }
  };

  const handleEditComment = async (commentId, newContent) => {
    try {
      await apiClient.put(`/comments/${commentId}`, { content: newContent });
      await loadComments();
      return true;
    } catch (error) {
      console.error("Erro ao editar comentário:", error);
      return false;
    }
  };

  const handleDeleteComment = async (commentId) => {
    try {
      await apiClient.delete(`/comments/${commentId}`);
      await loadComments();
      return true;
    } catch (error) {
      console.error("Erro ao excluir comentário:", error);
      return false;
    }
  };

  const submitComment = async (content, parentCommentId = null) => {
    if (!content.trim() || !user) return false;
    try {
      const payload = { id_user: user.id, id_parent_post: postId, id_parent_comment: parentCommentId, content: content.trim() };
      await apiClient.post(`/comments/submit`, payload, { headers: { 'Content-Type': 'application/json' } });
      loadComments();
      return true;
    } catch (err) {
      console.error('Erro ao enviar comentário:', err);
      return false;
    }
  };

  const handleNewComment = async () => {
    const success = await submitComment(newComment);
    if (success) {
      setNewComment('');
    }
  };

  const handleReply = async (parentCommentId, text) => {
    return await submitComment(text, parentCommentId);
  };

  if (loading) return <div><Header /><div className="text-center p-10">Carregando...</div></div>;
  if (!post) return <div><Header /><div className="text-center p-10">Post não encontrado.</div></div>;

  return (
    <div>
      <Header />
      <div className="max-w-2xl mx-auto mt-6 space-y-6 mb-8">
        <PostVoteHeader
          post={post}
          currentUserId={user.id}
          onEditRequest={handleEditRequest}
          onDeleteRequest={handleDeleteRequest}
          onReportRequest={handleReportRequest}
        />

        <div className="border rounded p-4 bg-white shadow-sm">
          {post.description && <div className="prose max-w-none mb-4 break-words">{post.description}</div>}
          {attachments.length > 0 && (
            <div className="border-t pt-4 mt-4">
              {attachments.map(att => <AttachmentDisplay key={att.id} attachment={att} />)}
            </div>
          )}
        </div>

        <div>
          <h2 className="text-xl font-semibold mb-4">Comentários</h2>
          <div className="mb-6">
            <MentionTextarea
              value={newComment}
              onChange={setNewComment}
              placeholder="Escreva seu comentário... Use u/username para mencionar."
              rows={3}
            />
            <button
              className="mt-2 px-4 py-1.5 bg-blue-600 text-white rounded hover:bg-blue-500 font-semibold"
              onClick={handleNewComment}
            >
              Enviar
            </button>
          </div>
          {commentsTree.map(c => (
            <CommentCard
              key={c.id}
              comment={c}
              currentUserId={user.id}
              onReply={handleReply}
              onEdit={handleEditComment}
              onDelete={handleDeleteComment}
            />
          ))}
        </div>
      </div>
      <EditPostModal
        post={editingPost}
        isOpen={isEditModalOpen}
        onClose={() => setIsEditModalOpen(false)}
        onSave={handleSaveEdit}
      />
    </div>
  );
}
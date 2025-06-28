import React, { useState, useEffect, useRef } from 'react';
import { ArrowUp, ArrowDown, MoreVertical, Edit, Trash2, ShieldAlert } from 'lucide-react';
import { Link } from 'react-router-dom';
import axios from 'axios';
import timeAgo from '../utils/timeAgo';
import MentionTextarea from './MentionTextarea';
import apiClient from '../api/axiosConfig';

export default function CommentCard({
  comment,
  currentUserId,
  level = 0,
  onReply,
  onEdit,  
  onDelete,
}) {
  const [score, setScore] = useState(0);
  const [userVote, setUserVote] = useState(null);
  const [replying, setReplying] = useState(false);
  const [replyText, setReplyText] = useState('');

  const [isMenuOpen, setIsMenuOpen] = useState(false);
  const [isEditing, setIsEditing] = useState(false);
  const [editedContent, setEditedContent] = useState(comment.content);
  const menuRef = useRef(null);
  const isOwner = String(comment.id_user) === String(currentUserId);

  useEffect(() => {
    const handleClickOutside = (event) => {
      if (menuRef.current && !menuRef.current.contains(event.target)) {
        setIsMenuOpen(false);
      }
    };
    document.addEventListener('mousedown', handleClickOutside);
    return () => document.removeEventListener('mousedown', handleClickOutside);
  }, []);

  const loadRating = async () => {
    if (!comment || !comment.id) return;
    try {
      const { data: scoreData } = await apiClient.get(`http://localhost:8080/ratings-in-comments/${comment.id}/votes`);
      setScore(scoreData.score);

      const { data: votes } = await apiClient.get(`http://localhost:8080/ratings-in-comments/${comment.id}/votes/list`);
      const myVote = votes.find(v => String(v.id_user) === String(currentUserId));
      setUserVote(myVote ? Boolean(Number(myVote.is_upvote)) : null);
    } catch (err) {
      if (err.response?.status !== 404) {
        console.error("Erro carregando ratings do comentário:", err);
      }
    }
  };

  useEffect(() => {
    loadRating();
  }, [comment.id, currentUserId]);

  const handleVote = async (isUp) => {
    try {
      await apiClient.post(`http://localhost:8080/ratings-in-comments/${comment.id}/votes`, { id_user: currentUserId, is_upvote: isUp });
      await loadRating();
    } catch (err) {
      console.error("Erro ao votar:", err);
    }
  };

  const submitReply = async () => {
    const success = await onReply(comment.id, replyText);
    if (success) {
      setReplyText('');
      setReplying(false);
    }
  };

  const handleEditClick = () => {
    setIsEditing(true);
    setIsMenuOpen(false);
  };

  const handleCancelEdit = () => {
    setIsEditing(false);
    setEditedContent(comment.content);
  };

  const handleSaveEdit = async () => {
    if (!editedContent.trim()) return;
    const success = await onEdit(comment.id, editedContent);
    if (success) {
      setIsEditing(false);
    }
  };

  const handleDeleteClick = () => {
    if (window.confirm("Tem certeza que deseja excluir este comentário? Esta ação não pode ser desfeita.")) {
      onDelete(comment.id);
    }
    setIsMenuOpen(false);
  };

  const handleReportClick = async () => {
    setIsMenuOpen(false);
    try {
      await apiClient.post(`http://localhost:8080/comments/${comment.id}/report`);
      alert("Comentário reportado com sucesso. A moderação irá analisar.");
    } catch (error) {
      console.error("Erro ao reportar comentário:", error);
      alert("Não foi possível reportar o comentário.");
    }
  };
  // --------------------------------------

  if (!comment) return null;

  return (
    <div className={`mb-4 rounded bg-white shadow-sm ${level > 0 ? "border-l-2 border-gray-200" : ""}`}>
      <div className="flex p-3">
        <div className="flex flex-col items-center w-12 text-gray-600 flex-shrink-0">
          <ArrowUp className={`w-5 h-5 cursor-pointer ${userVote === true ? "text-green-600" : "hover:text-green-500"}`} onClick={() => handleVote(true)} />
          <span className="font-bold mt-1">{score}</span>
          <ArrowDown className={`w-5 h-5 cursor-pointer ${userVote === false ? "text-red-600" : "hover:text-red-500"}`} onClick={() => handleVote(false)} />
        </div>

        <div className="flex-1 pl-4">
          <div className="flex justify-between items-center">
            <div className="text-sm text-gray-500">
              <Link to={`/users/${comment.id_user}`} className="font-semibold hover:underline">{comment.userName}</Link> • {timeAgo(comment.created_at)} ago
            </div>

            {!isEditing && (
              <div className="relative" ref={menuRef}>
                <button onClick={() => setIsMenuOpen(!isMenuOpen)} className="p-1 rounded-full hover:bg-gray-200">
                  <MoreVertical size={18} />
                </button>
                {isMenuOpen && (
                  <div className="absolute right-0 mt-2 w-36 bg-white border rounded shadow-lg z-10">
                    {isOwner && (
                      <>
                        <button onClick={handleEditClick} className="flex items-center w-full text-left px-3 py-2 text-sm hover:bg-gray-100">
                          <Edit size={14} className="mr-2" /> Editar
                        </button>
                        <button onClick={handleDeleteClick} className="flex items-center w-full text-left px-3 py-2 text-sm text-red-600 hover:bg-gray-100">
                          <Trash2 size={14} className="mr-2" /> Excluir
                        </button>
                      </>
                    )}
                    {!isOwner && (
                      <button onClick={handleReportClick} className="flex items-center w-full text-left px-3 py-2 text-sm hover:bg-gray-100">
                        <ShieldAlert size={14} className="mr-2" /> Reportar
                      </button>
                    )}
                  </div>
                )}
              </div>
            )}
          </div>
          
          <div className="mt-1">
            {isEditing ? (
              <div>
                <MentionTextarea
                  value={editedContent}
                  onChange={setEditedContent}
                  placeholder="Edite seu comentário..."
                  rows={3}
                />
                <div className="flex items-center space-x-2 mt-2">
                  <button onClick={handleSaveEdit} className="px-3 py-1 bg-blue-600 text-white text-xs font-semibold rounded hover:bg-blue-500">Salvar</button>
                  <button onClick={handleCancelEdit} className="px-3 py-1 bg-gray-200 text-xs font-semibold rounded hover:bg-gray-300">Cancelar</button>
                </div>
              </div>
            ) : (
              <p className="text-gray-800 break-words">{comment.content}</p>
            )}
          </div>

          {!isEditing && (
            <div className="text-sm text-gray-500 font-semibold cursor-pointer hover:underline mt-2" onClick={() => setReplying(!replying)}>
              comentar
            </div>
          )}
          {replying && (
            <div className="mt-2">
              <MentionTextarea
                value={replyText}
                onChange={setReplyText}
                placeholder="Sua resposta... Use u/username para mencionar."
                rows={2}
              />
              <button className="mt-1 px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-500" onClick={submitReply}>
                Enviar
              </button>
            </div>
          )}
        </div>
      </div>
      
      {comment.replies && comment.replies.map((reply) => (
        <div key={reply.id} className="pl-6">
          <CommentCard
            comment={reply}
            currentUserId={currentUserId}
            level={level + 1}
            onReply={onReply}
            onEdit={onEdit}
            onDelete={onDelete}
          />
        </div>
      ))}
    </div>
  );
}
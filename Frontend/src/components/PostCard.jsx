import React, { useEffect, useState, useRef, useCallback } from 'react';
import { Link } from 'react-router-dom';
import apiClient from '../api/axiosConfig';
import { ArrowUp, ArrowDown, MoreVertical, Edit, Trash2, ShieldAlert } from 'lucide-react';
import timeAgo from '../utils/timeAgo';

export default function PostCard({ post, currentUserId, onEditRequest, onDeleteRequest, onReportRequest }) {
  const [score, setScore] = useState(post.score || 0);
  const [userVote, setUserVote] = useState(null);
  const [isMenuOpen, setIsMenuOpen] = useState(false);
  const menuRef = useRef(null); 
  const isOwner = String(post.id_user) === String(currentUserId);

  const loadRating = useCallback(async () => {
    if (!post || !post.id) return;
    try {
      const scoreRes = await apiClient.get(`/ratings-in-posts/${post.id}/score`);
      setScore(scoreRes.data.score);

      const votesRes = await apiClient.get(`/ratings-in-posts/${post.id}/votes`);
      const voteObj = votesRes.data.find((v) => String(v.id_user) === String(currentUserId));
      setUserVote(voteObj ? Boolean(Number(voteObj.is_upvote)) : null);
    } catch (err) {
      if (err.response?.status !== 401) {
          console.error('Erro carregando ratings:', err);
      }
    }
  }, [post, currentUserId]);

  useEffect(() => {
    loadRating();
  }, [loadRating]); 

  useEffect(() => {
    const handleClickOutside = (event) => {
      if (menuRef.current && !menuRef.current.contains(event.target)) {
        setIsMenuOpen(false);
      }
    };
    document.addEventListener('mousedown', handleClickOutside);
    return () => document.removeEventListener('mousedown', handleClickOutside);
  }, []);

  const handleVote = async (isUp) => {
    try {
      await apiClient.post(
        `/ratings-in-posts/${post.id}/votes`,
        { id_user: currentUserId, is_upvote: isUp }
      );
      await loadRating();
    } catch (err) {
      if (err.response?.status !== 401) {
        console.error('Erro no voto:', err);
      }
    }
  };
  // ------------------------------------

  if (!post) {
    return null;
  }

  return (
    <div className="flex border rounded p-4 mb-4 bg-white shadow-sm hover:border-gray-400 transition-colors">
      <div className="flex flex-col items-center w-12 text-gray-600">
        <ArrowUp
          onClick={() => handleVote(true)}
          className={`w-5 h-5 cursor-pointer ${userVote === true ? 'text-green-600 hover:text-green-500' : 'text-gray-400 hover:text-green-500'}`}
        />
        <span className="font-bold mt-1">{score}</span>
        <ArrowDown
          onClick={() => handleVote(false)}
          className={`w-5 h-5 cursor-pointer ${userVote === false ? 'text-red-600 hover:text-red-500' : 'text-gray-400 hover:text-red-500'}`}
        />
      </div>
      <div className="flex-1 pl-4">
        <div className="flex justify-between items-start">
          <div className="flex-1">
            <Link to={`/posts/${post.id}`} className="text-xl font-medium text-gray-900 hover:underline">
              {post.title}
            </Link>
            <div className="text-sm text-gray-500 mt-1">
              Submitted {timeAgo(post.posted_at)} ago by{' '}
              <Link to={`/users/${post.id_user}`} className="font-semibold hover:underline">u/{post.author}</Link>
              {!post.isProfilePost && (
                <>
                  {' to '}
                  <Link to={`/communities/${post.id_community}`} className="font-semibold hover:underline">r/{post.community}</Link>
                </>
              )}
            </div>
          </div>
          <div className="relative" ref={menuRef}>
            <button onClick={() => setIsMenuOpen(prev => !prev)} className="p-1 rounded-full hover:bg-gray-200">
              <MoreVertical size={18} />
            </button>
            {isMenuOpen && (
              <div className="absolute right-0 mt-2 w-36 bg-white border rounded shadow-lg z-10">
                {isOwner ? (
                  <>
                    <button onClick={() => { onEditRequest(post); setIsMenuOpen(false); }} className="flex items-center w-full text-left px-3 py-2 text-sm hover:bg-gray-100">
                      <Edit size={14} className="mr-2" />Editar
                    </button>
                    <button onClick={() => { onDeleteRequest(post.id); setIsMenuOpen(false); }} className="flex items-center w-full text-left px-3 py-2 text-sm text-red-600 hover:bg-gray-100">
                      <Trash2 size={14} className="mr-2" />Excluir
                    </button>
                  </>
                ) : (
                  <button onClick={() => { onReportRequest(post.id); setIsMenuOpen(false); }} className="flex items-center w-full text-left px-3 py-2 text-sm hover:bg-gray-100">
                    <ShieldAlert size={14} className="mr-2" />Reportar
                  </button>
                )}
              </div>
            )}
          </div>
        </div>
        <div className="text-sm text-gray-600 mt-2">
          <Link to={`/posts/${post.id}`} className="hover:underline">
            {post.commentsCount} comment{post.commentsCount !== 1 && 's'}
          </Link>
        </div>
      </div>
    </div>
  );
}
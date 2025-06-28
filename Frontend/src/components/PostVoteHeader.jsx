import React, { useEffect, useState, useRef } from 'react';
import { ArrowUp, ArrowDown, MoreVertical, Edit, Trash2, ShieldAlert } from 'lucide-react';
import { Link } from 'react-router-dom';
import axios from 'axios';
import timeAgo from '../utils/timeAgo';
import apiClient from '../api/axiosConfig';

export default function PostVoteHeader({
  post, 
  currentUserId,
  onEditRequest,
  onDeleteRequest,
  onReportRequest,
}) {
  const [score, setScore] = useState(0);
  const [userVote, setUserVote] = useState(null);
  const [isMenuOpen, setIsMenuOpen] = useState(false);
  const menuRef = useRef(null);

  const isOwner = post && String(post.id_user) === String(currentUserId);

  useEffect(() => {
    const loadRating = async () => {
      if (!post || !post.id) return;
      try {
        const scoreRes = await apiClient.get(`http://localhost:8080/ratings-in-posts/${post.id}/score`);
        setScore(scoreRes.data.score);
        const votesRes = await apiClient.get(`http://localhost:8080/ratings-in-posts/${post.id}/votes`);
        const yourVote = votesRes.data.find(v => String(v.id_user) === String(currentUserId));
        setUserVote(yourVote ? Boolean(Number(yourVote.is_upvote)) : null);
      } catch (error) {
        if (error.response?.status !== 404) {
          console.error(`Erro ao carregar rating para post ${post.id}:`, error);
        }
      }
    };
    if (post && currentUserId) {
      loadRating();
    }
  }, [post, currentUserId]);

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
    await apiClient.post(`http://localhost:8080/ratings-in-posts/${post.id}/votes`, { id_user: currentUserId, is_upvote: isUp });
    const scoreRes = await apiClient.get(`http://localhost:8080/ratings-in-posts/${post.id}/score`);
    setScore(scoreRes.data.score);
    const votesRes = await apiClient.get(`http://localhost:8080/ratings-in-posts/${post.id}/votes`);
    const yourVote = votesRes.data.find(v => String(v.id_user) === String(currentUserId));
    setUserVote(yourVote ? Boolean(Number(yourVote.is_upvote)) : null);
  };

  if (!post) return null;

  return (
    <div className="flex border rounded p-4 bg-white shadow-sm mb-4">
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
            <div className="flex-1 pr-4">
                <h1 className="text-2xl font-bold break-words">{post.title}</h1>
                <div className="text-sm text-gray-500 mt-1">
                    Submitted {timeAgo(post.posted_at)} ago by{' '}
                    <Link to={`/users/${post.id_user}`} className="font-semibold hover:underline">u/{post.userName}</Link>
                    {!post.isProfilePost && post.id_community && (
                        <>
                        {' to '}
                        <Link to={`/communities/${post.id_community}`} className="font-semibold hover:underline">r/{post.communityName}</Link>
                        </>
                    )}
                </div>
            </div>
            <div className="relative" ref={menuRef}>
                <button onClick={() => setIsMenuOpen(prev => !prev)} className="p-1 rounded-full hover:bg-gray-200">
                <MoreVertical size={20} />
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
      </div>
    </div>
  );
}
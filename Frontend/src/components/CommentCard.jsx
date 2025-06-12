// src/components/CommentCard.jsx
import React, { useState, useEffect } from 'react';
import { ArrowUp, ArrowDown } from 'lucide-react';
import { Link } from 'react-router-dom';
import axios from 'axios';
import timeAgo from '../utils/timeAgo';

export default function CommentCard({
  comment,
  currentUserId,
  level = 0,
  onReply,
}) {
  const [score, setScore]       = useState(0);
  const [userVote, setUserVote] = useState(null);
  const [replying, setReplying] = useState(false);
  const [replyText, setReplyText] = useState('');

  // Carrega score e voto do usuário
  const loadRating = async () => {
    try {
      // 1) busca o score correto
      const { data: scoreData } = await axios.get(
        `http://localhost:8080/ratings-in-comments/${comment.id}/votes`
      );
      setScore(scoreData.score);

      // 2) busca a lista de votos
      const { data: votes } = await axios.get(
        `http://localhost:8080/ratings-in-comments/${comment.id}/votes/list`
      );
      const myVote = votes.find(
        v => String(v.id_user) === String(currentUserId)
      );
      setUserVote(myVote ? Boolean(Number(myVote.is_upvote)) : null);

    } catch (err) {
      // Se der 404 no score, assume que é zero
      if (err.response?.status === 404) {
        setScore(0);
        setUserVote(null);
      } else {
        console.error('Erro carregando ratings:', err);
      }
    }
  };

  useEffect(() => {
    loadRating();
  }, []);

  const handleVote = async (isUp) => {
    try {
      await axios.post(
        `http://localhost:8080/ratings-in-comments/${comment.id}/votes`,
        { id_user: currentUserId, is_upvote: isUp }
      );
      await loadRating();
    } catch (err) {
      console.error('Erro ao votar:', err);
    }
  };

  const submitReply = () => {
    onReply(comment.id, replyText);
    setReplyText('');
    setReplying(false);
  };

  return (
    <div
      className={`mb-4 rounded p-3 bg-white shadow-sm ${
        level > 0 ? 'border-l-2 border-gray-200 pl-4' : ''
      }`}
    >
      <div className="flex rounded p-3 bg-white">
        {/* Votação */}
        <div className="flex flex-col items-center w-12 text-gray-600">
          <ArrowUp
            className={`w-5 h-5 cursor-pointer ${
              userVote === true ? 'text-green-600' : 'hover:text-green-500'
            }`}
            onClick={() => handleVote(true)}
          />
          <span className="font-bold mt-1">{score}</span>
          <ArrowDown
            className={`w-5 h-5 cursor-pointer ${
              userVote === false ? 'text-red-600' : 'hover:text-red-500'
            }`}
            onClick={() => handleVote(false)}
          />
        </div>

        {/* Conteúdo */}
        <div className="flex-1 pl-4">
          <div className="text-sm text-gray-500">
            <Link to={`/users/${comment.id_user}`} className="font-semibold hover:underline">
              {comment.userName}
            </Link>{' '}
            • {timeAgo(comment.created_at.date)} ago
          </div>
          <div className="mt-1">{comment.content}</div>

          {/* botão comentar */}
          <div
            className="text-sm text-gray-500 font-semibold cursor-pointer hover:underline mt-2"
            onClick={() => setReplying(!replying)}
          >
            comentar
          </div>

          {/* textarea de resposta */}
          {replying && (
            <div className="mt-2">
              <textarea
                className="w-full border rounded p-2"
                rows="2"
                placeholder="Sua resposta..."
                value={replyText}
                onChange={e => setReplyText(e.target.value)}
              />
              <button
                className="mt-1 px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-500"
                onClick={submitReply}
              >
                Enviar
              </button>
            </div>
          )}
        </div>
      </div>

      {/* Replies recursivas */}
      {comment.replies.map(reply => (
        <CommentCard
          key={reply.id}
          comment={reply}
          currentUserId={currentUserId}
          level={level + 1}
          onReply={onReply}
        />
      ))}
    </div>
  );
}
